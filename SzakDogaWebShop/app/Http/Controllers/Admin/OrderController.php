<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10); 
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    $total = collect($cart)->sum(fn($item) =>
        $item['price'] * $item['quantity'] * (1 - ($item['discount'] ?? 0) / 100)
    );

    $order = Order::create([
        'user_id' => auth::id(),
        'status' => 'pending',
        'total' => $total,
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    foreach ($cart as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'] * (1 - ($item['discount'] ?? 0) / 100),
        ]);
    }

    Session::forget('cart');

    return redirect()->route('home')->with('success', 'Rendelés sikeresen leadva!');
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product','user')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,cancelled',
        ]);
        
        $order->update($validated);

        return redirect()->route('admin.orders.index')
                                 ->with('success','Rendelés státusza frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function checkout()
{
    $cart = session()->get('cart', []);
    $total = array_sum(array_map(fn($item) =>
        $item['price'] * (1 - ($item['discount'] ?? 0) / 100) * $item['quantity'],
        $cart
    ));

    return view('checkout', compact('cart', 'total'));
}

public function updateStatus(Request $request, Order $order): RedirectResponse
{
    $request->validate([
        'status' => 'required|string|in:pending,processing,shipped,completed,paid,cancelled',
    ]);

    $order->update([
        'status' => $request->status,
    ]);

    return back()->with('success', 'Státusz frissítve!');
}




}
