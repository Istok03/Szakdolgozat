<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
  public function index(Request $request)
{
    $query = Product::query();

    if ($request->name) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }

    if ($request->min) {
        $query->where('price', '>=', $request->min);
    }

    if ($request->max) {
        $query->where('price', '<=', $request->max);
    }

    if ($request->sale == 1) {
        $query->where('discount', '>', 0);
    }

    $products = $query->get();

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

public function show($id){
    $product = Product::findOrFail($id);
    return view('product.show', compact('product'));    
}



}
