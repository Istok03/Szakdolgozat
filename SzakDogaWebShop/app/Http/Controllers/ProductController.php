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
       
        if($request->filled('search')){
            $query -> where('name','like','%' .  $request->search .'%');
        }
       
        if($request->filled('min_price')){
            $query -> where('price','>=', $request->min_price);
        }
       
        if($request->filled('max_price')){
            $query -> where('price','>=', $request->max_price);
        }

        if($request->has('discount_only')){
            $query -> where('discount','>',0.);
        }

        $products = $query -> get();
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
