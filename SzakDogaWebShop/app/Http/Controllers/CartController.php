<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;


class CartController extends Controller{
    public function add( $id)
    {
        $product = Product::findOrFail( $id );
        $cart = session() -> get("cart",[]);

        if( isset( $cart[$id] )) {
            $cart[$id] ['quantity']++;
        }
        else{
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "discount" => $product->discount,
                "image" => $product->image
            ];
        }
        session()->put("cart", $cart );
        return redirect()->back()->with('success', 'Termék sikeresen hozzáadva a kosárhoz!');
    }

    public function index()
    {
        $cart = session() -> get('cart',[]);
        return view('cart', compact('cart'));
    }
   
    public function remove ( $id )
    {
        $cart = session() -> get('cart',[]);
        if( isset( $cart[$id] )) {
            unset( $cart[$id] );
            session()->put('cart', $cart );
        }
        return redirect()->back()->with('success','Termék eltávolítva a kosárból!');
    }
}
?>