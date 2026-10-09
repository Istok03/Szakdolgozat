<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);
        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Rendelés frissítve!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]));

        return back()->with('success', 'Státusz frissítve!');
    }
}