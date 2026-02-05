<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
   

    public function store (Request $request, Product $product){
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

       $review = Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,

            ]
       );

        return redirect()->back()->with('success', 'Vélemény sikeresen mentve!');
    }

    public function destroy(Review $review){
        if(Auth::id() !== $review->user_id && !Auth::user()->is_admin){
            abort(403);
        }
    
    $review->delete();

    return back()->with('success' , 'Vélemény sikeresen törölve!');
    }
    
}
