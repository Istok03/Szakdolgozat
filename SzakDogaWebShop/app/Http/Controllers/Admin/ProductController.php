<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use League\CommonMark\Extension\DescriptionList\Node\Description;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all(); 
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=> 'required|string|max:255',
            'price'=>'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description' =>'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/products'),
 $filename); $validated['image'] = 'images/products/' . $filename; }


        Product:: create($validated);

        return redirect()->route('admin.products.index')
                                  ->with('success','Termék sikeresen létrehozva!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
         'name' => 'required|string|max:255',
         'price' => 'required|numeric|min:0', 
         'discount' => 'nullable|integer|min:0|max:100', 
         'description' => 'nullable|string',
         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/products'),$filename);
            $validated['image'] = 'images/products/'. $filename;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
                                  ->with('success','Termék sikeresen frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $product = Product::findOrFail($id);  
         if ($product->image && file_exists(public_path($product->image))) {
             unlink(public_path($product->image)); 
            } 
        
            $product->delete();
            
            return redirect()->route('admin.products.index')
                                      ->with('success', 'Termék sikeresen törölve!');
    }
}
