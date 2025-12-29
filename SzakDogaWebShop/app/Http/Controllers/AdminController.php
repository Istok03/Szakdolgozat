<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function index():View
    {   
        $productCount = Product::count();
        $pendingOrders = Order::where('status', 'pending') -> count();
        $adminCount = User::where ('is_admin',true) -> count();
        $orders= Order::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('productCount','pendingOrders','adminCount'));
     }
}
