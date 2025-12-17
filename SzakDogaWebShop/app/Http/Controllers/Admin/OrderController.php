<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('checkout')->with('error', 'A kosár üres.');
    }

    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'address' => 'required|string',
    ]);

    DB::transaction(function () use ($request, $cart) {
        $order = \App\Models\Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_price' => array_sum(array_map(fn($item) => $item['price'] * (1 - ($item['discount'] ?? 0) / 100) * $item['quantity'], $cart)),
            'status' => 'Feldolgozás alatt'
        ]);

        foreach ($cart as $id => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }
    });

    session()->forget('cart');

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





}
