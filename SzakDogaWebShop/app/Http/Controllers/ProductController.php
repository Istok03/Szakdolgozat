<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

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

    if ($request->has('category')) {
        $query->where('category_id', $request->category);
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

    $randomReviews = \App\Models\Review::with('user', 'product')
        ->inRandomOrder()
        ->take(3)
        ->get();

    return view('home', [
        'products' => $products,
        'randomReviews' => $randomReviews
    ]);
}


public function show($id){
    $product = Product::with(['category.options', 'reviews.user'])->findOrFail($id);
    return view('product.show', compact('product'));
}

public function byCategory($id)
{
    $category = Category::findOrFail($id);

    $products = Product::where('category_id', $category->id)->get();

    return view('product.index', compact('products', 'category'));
}



public function edit(string $id)
{
    $product = Product::findOrFail($id);
    return view('admin.products.edit', compact('product'));
}



}?>
