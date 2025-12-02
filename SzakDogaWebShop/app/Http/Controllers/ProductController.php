<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        return view('products', compact('products'));
    }

    public function sales(){
    $products = Product::where('discount', '>', 0)->get();
    return view('sales', compact('products'));
}

public function home(){
    $products = Product::orderBy('created_at','desc')->take(4)->get();
    return view('home', compact('products'));
}

}
