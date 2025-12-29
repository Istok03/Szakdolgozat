<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        return view('cart', compact('cart', 'total'));
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);
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

        return response()->json([
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
        ]);
    }

    public function increase($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        }
        session()->put('cart', $cart);

        return response()->json([
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
        ]);
    }

    public function decrease($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id]) && $cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
        }
        session()->put('cart', $cart);

        return response()->json([
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
        ]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        session()->put('cart', $cart);

        return response()->json([
            'cart' => $cart,
            'total' => $this->calculateTotal($cart)
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
}
