<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->cart();
        $total = $this->total($cart);

        return view('cart', compact('cart', 'total'));
    }

    public function add($id)
    {
        return $this->change($id, 'add');
    }

    public function increase($id)
    {
        return $this->change($id, 'increase');
    }

    public function decrease($id)
    {
        return $this->change($id, 'decrease');
    }

    public function remove($id)
    {
        return $this->change($id, 'remove');
    }

    private function change($id, string $action)
    {
        $product = in_array($action, ['add', 'increase']) ? Product::findOrFail($id) : null;

        if (Auth::check()) {
            DB::transaction(function () use ($id, $action) {
                User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
                $item = CartItem::firstOrNew(['user_id' => Auth::id(), 'product_id' => $id]);
                if ($action === 'remove') {
                    if ($item->exists) {
                        $item->delete();
                    }
                } elseif ($item->exists || $action === 'add') {
                    $quantity = $item->exists ? $item->quantity : 0;
                    $item->quantity = max(1, $quantity + ($action === 'decrease' ? -1 : 1));
                    $item->save();
                }
            });
        } else {
            $cart = session('cart', []);
            if ($action === 'remove') {
                unset($cart[$id]);
            } elseif (isset($cart[$id]) || $action === 'add') {
                $quantity = max(1, ($cart[$id]['quantity'] ?? 0) + ($action === 'decrease' ? -1 : 1));
                $cart[$id] = $product ? $this->line($product, $quantity) : array_replace($cart[$id], ['quantity' => $quantity]);
            }
            session()->put('cart', $cart);
        }

        $cart = $this->cart();

        return response()->json(['cart' => $cart, 'total' => $this->total($cart)]);
    }

    private function cart(): array
    {
        if (!Auth::check()) {
            return session('cart', []);
        }

        return CartItem::with('product')->where('user_id', Auth::id())->get()
            ->filter(fn ($item) => $item->product !== null)
            ->mapWithKeys(fn ($item) => [$item->product_id => $this->line($item->product, $item->quantity)])
            ->all();
    }

    private function line(Product $product, int $quantity): array
    {
        return [
            'id' => $product->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'discount' => $product->discount ?? 0,
            'quantity' => $quantity,
            'image' => $product->image,
        ];
    }

    private function total(array $cart): float
    {
        return round(collect($cart)->sum(fn ($item) =>
            round($item['price'] * (1 - ($item['discount'] ?? 0) / 100), 2) * $item['quantity']
        ), 2);
    }
}