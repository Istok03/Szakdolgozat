<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->withSum('items', 'quantity')->latest('id')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, string $id)
    {
        $order = $this->customerOrder($request, $id);

        return view('orders.show', ['order' => $order, 'confirmed' => false]);
    }

    public function confirmation(Request $request, string $id)
    {
        $order = $this->customerOrder($request, $id);
        if ((string) $request->session()->get('completed_order_id') !== (string) $order->id) {
            return redirect()->route('orders.show', $order);
        }

        return view('orders.show', ['order' => $order, 'confirmed' => true]);
    }

    private function customerOrder(Request $request, string $id): Order
    {
        return Order::where('user_id', $request->user()->id)
            ->with('items.product')->findOrFail($id);
    }

    public function checkout(Request $request)
    {
        $items = CartItem::with('product')->where('user_id', $request->user()->id)->get();
        $cart = $items->mapWithKeys(fn ($item) => [$item->product_id => [
            'name' => $item->product->name,
            'price' => $item->product->price,
            'discount' => $item->product->discount ?? 0,
            'quantity' => $item->quantity,
            'stock' => $item->product->stock,
        ]])->all();
        $total = $items->sum(fn ($item) => $item->product->salePrice() * $item->quantity);

        $shippingMethods = config('shipping.methods');
        $selectedShipping = $request->old('shipping_method', config('shipping.default'));
        if (!is_string($selectedShipping) || !isset($shippingMethods[$selectedShipping])) {
            $selectedShipping = config('shipping.default');
        }

        return view('checkout', compact('cart', 'total', 'shippingMethods', 'selectedShipping'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|in:cod',
            'shipping_method' => ['required', Rule::in(array_keys(config('shipping.methods')))],
        ], [
            'shipping_method.required' => 'Válassz szállítási módot.',
            'shipping_method.in' => 'A választott szállítási mód nem érhető el.',
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            // Serialize submissions for this customer before reading the cart.
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $items = CartItem::query()
                ->where('user_id', $request->user()->id)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'A kosár üres.']);
            }

            // Lock shared products in a stable order across different customers.
            $products = Product::whereIn('id', $items->pluck('product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            foreach ($items as $item) {
                $item->setRelation('product', $products->get($item->product_id));
                if (!$item->product || $item->quantity < 1) {
                    throw ValidationException::withMessages(['cart' => 'A kosár tartalma megváltozott. Ellenőrizd újra.']);
                }
                if ($item->quantity > $item->product->stock) {
                    throw ValidationException::withMessages(['cart' => "A(z) {$item->product->name} termékből csak {$item->product->stock} db érhető el. Módosítsd a kosarat."]);
                }
            }

            $shipping = config('shipping.methods.'.$validated['shipping_method']);
            $order = Order::create($validated + [
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total' => round($items->sum(fn ($item) => $item->product->salePrice() * $item->quantity) + $shipping['fee'], 2),
                'shipping_fee' => $shipping['fee'],
                'shipping_label' => $shipping['label'],
                'pickup_address' => $validated['shipping_method'] === 'pickup' ? config('shipping.pickup_address') : null,
                'manages_stock' => true,
                'stock_reserved' => true,
            ]);

            foreach ($items as $item) {
                $item->product->decrement('stock', $item->quantity);
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->salePrice(),
                ]);
            }

            CartItem::whereIn('id', $items->modelKeys())->delete();

            return $order;
        });

        return redirect()->route('orders.confirmation', $order)->with('completed_order_id', $order->id);
    }
}
