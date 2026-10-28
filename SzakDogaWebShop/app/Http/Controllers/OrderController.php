<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        ]])->all();
        $total = $items->sum(fn ($item) => $item->product->salePrice() * $item->quantity);

        return view('checkout', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|in:cod',
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            // Serialize submissions for this customer before reading the cart.
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $items = CartItem::with('product')
                ->where('user_id', $request->user()->id)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'A kosár üres.']);
            }

            foreach ($items as $item) {
                if (!$item->product || $item->quantity < 1) {
                    throw ValidationException::withMessages(['cart' => 'A kosár tartalma megváltozott. Ellenőrizd újra.']);
                }
            }

            $order = Order::create($validated + [
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'total' => $items->sum(fn ($item) => $item->product->salePrice() * $item->quantity),
            ]);

            foreach ($items as $item) {
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
