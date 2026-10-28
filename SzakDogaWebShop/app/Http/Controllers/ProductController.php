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
        return view('products', ['products' => $this->filteredProducts($request)]);
    }

    public function sales(Request $request)
    {
        return view('sales', ['products' => $this->filteredProducts($request, true)]);
    }

    private function filteredProducts(Request $request, bool $onlySale = false)
    {
        $filters = $request->validate([
            'name' => 'nullable|string|max:255',
            'min' => 'nullable|numeric|min:0',
            'max' => ['nullable', 'numeric', 'min:0', ...($request->filled('min') ? ['gte:min'] : [])],
            'sale' => 'nullable|boolean',
            'category' => 'nullable|integer|exists:categories,id',
        ], [
            'max.gte' => 'A maximum ár nem lehet kisebb a minimum árnál.',
            'numeric' => 'Az ár csak szám lehet.',
            'min' => 'Az ár nem lehet negatív.',
            'category.exists' => 'A kiválasztott kategória nem található.',
        ]);

        $query = Product::query();
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }
        // Match the rounded, discounted unit price used by the cart and checkout.
        $price = 'ROUND(price * (1 - COALESCE(discount, 0) / 100.0), 2)';
        if (isset($filters['min'])) {
            $query->whereRaw($price.' >= CAST(? AS DECIMAL(14, 2))', [$filters['min']]);
        }
        if (isset($filters['max'])) {
            $query->whereRaw($price.' <= CAST(? AS DECIMAL(14, 2))', [$filters['max']]);
        }
        if ($onlySale || ($filters['sale'] ?? false)) {
            $query->where('discount', '>', 0);
        }
        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        return $query->latest('id')->paginate(12)->withQueryString();
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

    return redirect()->route('products', ['category' => $category->id]);
}



public function edit(string $id)
{
    $product = Product::findOrFail($id);
    return view('admin.products.edit', compact('product'));
}



}?>
