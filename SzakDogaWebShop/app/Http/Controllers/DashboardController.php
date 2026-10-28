<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id);

        return view('dashboard', [
            'orderCount' => (clone $orders)->count(),
            'latestOrder' => $orders->latest('id')->first(),
            'cartQuantity' => CartItem::where('user_id', $request->user()->id)->sum('quantity'),
        ]);
    }
}
