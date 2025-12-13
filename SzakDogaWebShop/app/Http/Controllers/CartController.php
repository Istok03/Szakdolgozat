<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Models\Product;


    class CartController extends Controller{

        public function add( $id)
        {
            $product = Product::findOrFail( $id );
            $cart = session() -> get("cart",[]);
            $total = 0;
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
            return response()->json( [
                'success' => true,
                'count' => array_sum(array_column($cart,'quantity')),
            ]);
        }

    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;
        foreach ($cart as $item) {
            $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
            $total += $price * $item['quantity'];
        }
        return view('cart', compact('cart', 'total'));
    }
    
        public function remove ( $id )
        {
            $cart = session()->get('cart', []);

            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }

            $total = 0;
            foreach ($cart as $item) {
                $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
                $total += $price * $item['quantity'];
            }

            return view('cart_content', [
                'cart' => $cart,
                'total' => $total
            ]);
        }

    public function increase($id){
    $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
            session()->put('cart', $cart);
        }

        $total = 0;
        foreach ($cart as $item) {
            $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
            $total += $price * $item['quantity'];
        }

        return view('cart_content', compact('cart', 'total'));
    }


    public function decrease($id){
        $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity']--;
                session()->put('cart', $cart);
            }

            $total = 0;
            foreach ($cart as $item) {
                $price = $item['price'] * (1 - ($item['discount'] ?? 0) / 100);
                $total += $price * $item['quantity'];
            }

            return view('cart_content', compact('cart', 'total'));
    }
    }



    ?>