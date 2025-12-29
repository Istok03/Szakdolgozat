<?php

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
    $cart = Session::get('cart', []);
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity'] * (1 - ($item['discount'] ?? 0) / 100));

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

    $cart = Session::get('cart', []);
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

    foreach ($cart as $productId =>$item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'quantity' => $item['quantity'],
            'price' => $item['price'] * (1 - ($item['discount'] ?? 0) / 100),
        ]);
    }

    Session::forget('cart');

    return redirect()->route('home')->with('success', 'Rendelés sikeresen leadva!');
}
}
?>