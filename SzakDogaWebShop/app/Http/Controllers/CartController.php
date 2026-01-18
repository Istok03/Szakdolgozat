<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::check()
            ? $this->getUserCart()
            : session()->get('cart', []);

        $total = $this->calculateTotal($cart);

        return view('cart', compact('cart', 'total'));
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);

        if (Auth::check()) {
            $item = CartItem::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->first();

            if ($item) {
                $item->quantity++;
                $item->save();
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $id,
                    'quantity' => 1
                ]);
            }

            $cart = $this->getUserCart();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    "id" => $product->id,
                    "product_id" => $product->id,
                    "name" => $product->name,
                    "price" => $product->price,
                    "quantity" => 1,
                    "image" => $product->image,
                    "discount" => $product->discount ?? 0
                ];
            }
            session()->put('cart', $cart);
        }

        $total = $this->calculateTotal($cart);

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function increase($id)
    {
        if (Auth::check()) {
            $item = CartItem::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->first();

            if ($item) {
                $item->quantity++;
                $item->save();
            }

            $cart = $this->getUserCart();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            }
            session()->put('cart', $cart);
        }

        $total = $this->calculateTotal($cart);

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function decrease($id)
    {
        if (Auth::check()) {
            $item = CartItem::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->first();

            if ($item && $item->quantity > 1) {
                $item->quantity--;
                $item->save();
            }

            $cart = $this->getUserCart();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id]) && $cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            }
            session()->put('cart', $cart);
        }

        $total = $this->calculateTotal($cart);

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function remove($id)
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->delete();

            $cart = $this->getUserCart();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        $total = $this->calculateTotal($cart);

        return response()->json([
            'cart' => $cart,
            'total' => $total
        ]);
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
            $total += $price * $item['quantity'];
        }
        return $total;
    }

    private function getUserCart()
    {
        $items = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $cart = [];
        foreach ($items as $item) {
            $cart[$item->product_id] = [
                "id" => $item->product_id,
                "product_id" => $item->product_id,
                "name" => $item->product->name,
                "price" => $item->product->price,
                "quantity" => $item->quantity,
                "image" => $item->product->image,
                "discount" => $item->product->discount ?? 0
            ];
        }

        return $cart;
    }
}
