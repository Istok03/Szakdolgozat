<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
   
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

 
    public function create()
    {
        return view('admin.categories.create');
    }

 
    public function store(Request $request)
    {

       $request->validate(['name'=> 'required|string|max:255']);
        Category::create(['name'=> $request->name]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategória  létrehozva.');
    }
        
    


    public function show(string $id)
    {
        //
    }

   
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

  
    public function update(Request $request, string $id)
    {
        $request->validate(['name'=> 'required|string|max:255']);
        Category::findOrFail($id)->update(['name'=> $request->name]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategória frissítve.');
    }

  
    public function destroy(string $id)
    {
        Category::findOrFail($id)->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategória törölve.');
    }
}
