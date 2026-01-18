<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

public function checkout()
{
    if (Auth::check()) {
        // DB kosár
        $items = \App\Models\CartItem::with('product')
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

    } else {
        // Vendég kosár
        $cart = Session::get('cart', []);
    }

    $total = collect($cart)->sum(fn($item) =>
        $item['price'] * $item['quantity'] * (1 - ($item['discount'] ?? 0) / 100)
    );

    return view('checkout', compact('cart', 'total'));
}




public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'address' => 'required|string',
    ]);

    if (Auth::check()) {
        $items = \App\Models\CartItem::with('product')
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

    } else {
        $cart = Session::get('cart', []);
    }

    if (empty($cart)) {
        return back()->with('error', 'A kosár üres.');
    }

    $total = 0;
    foreach ($cart as $item) {
        $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
        $total += $price * $item['quantity'];
    }

    $order = Order::create([
        'user_id' => Auth::id(),
        'status' => 'pending',
        'total' => $total,
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    foreach ($cart as $productId => $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'quantity' => $item['quantity'],
            'price' => $item['price'] * (1 - ($item['discount'] ?? 0) / 100),
        ]);
    }

    if (Auth::check()) {
        \App\Models\CartItem::where('user_id', Auth::id())->delete();
    } else {
        Session::forget('cart');
    }

    if ($request->payment_method === 'card') {
    $request->validate([
        'card_number' => 'required',
        'card_exp' => 'required',
        'card_cvc' => 'required',
    ]);
}
    return redirect()->route('home')->with('success', 'Rendelés sikeresen leadva!');
   

}
}
?>