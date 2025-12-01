<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/products', function () {
    return view('products');
})->name('products');

Route::get('/offers', function () {
    return view('sales');
})->name('offers');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/cart', function () {
    return view('cart');
})->name('cart');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/offers', function(){
    $sales = [
        [
            'name' => 'Gaming Laptop',
            'image' =>'images/products/laptop.png',
            'old_price' => 399000,
            'sale_price' => 299000,
            'discount' => 25
        ],
        [
            'name' => 'RGB Egér',
            'image' => 'images/product/mouse.png',
            'old_price'=>12990,
            'sale_price' => 9990,
            'discount' => 20
        ],
        [
            'name' => 'Gaming Headset',
            'image' => 'images/product/headset.png',
            'old_price'=>24990,
            'sale_price' => 19990,
            'discount' => 20
        ],
    ];

    return view('sales', compact('sales'));
})->name('offers');



require __DIR__.'/auth.php';
