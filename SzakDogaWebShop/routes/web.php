<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; 
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])
->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');


Route::post('/cart/add/{id}', [CartController::class, 'add'
])->name('cart.add');

Route::get('/cart', [CartController::class, 'index'
])->name('cart.index');

Route::post('/cart/remove/{id}', [CartController::class, 'remove'
])->name('cart.remove');

Route::post('/cart/increase/{id}', [CartController::class, 'increase'
])->name('cart.increase');

Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'
])->name('cart.decrease');


Route::get('/payment', function () {
    return view('payment');
})->name('payment');

Route::get('/products', [ProductController::class, 'index'
])->name('products');

Route::get('/offers', [ProductController::class, 'sales'])
->name('offers');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/product/{id}', [ProductController::class, 'show'
])->name('product.show');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

 Route::middleware(['auth', 'admin'])->group(function () { 
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/products', AdminProductController::class); 
});



require __DIR__.'/auth.php';
