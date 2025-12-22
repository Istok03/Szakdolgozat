<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;
use App\Models\CartItem;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $sessionCart = session()->get('cart', []);
    $user = Auth::user();

    foreach ($sessionCart as $productId => $item) {
        CartItem::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $productId],
            ['quantity' => DB::raw('quantity + ' . $item['quantity'])]
        );
    }

    session()->forget('cart');

    if($user->is_admin){
        return redirect()->intended('/admin');
    }

    return redirect()->intended('/dashboard');
}



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
