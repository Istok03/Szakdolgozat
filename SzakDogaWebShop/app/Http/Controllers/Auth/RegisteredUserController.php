<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Add meg a neved.',
            'name.string' => 'A név szöveg legyen.',
            'name.max' => 'A név legfeljebb :max karakter lehet.',
            'email.required' => 'Add meg az e-mail-címed.',
            'email.string' => 'Az e-mail-cím szöveg legyen.',
            'email.lowercase' => 'Az e-mail-címet kisbetűkkel add meg.',
            'email.email' => 'Adj meg egy érvényes e-mail-címet.',
            'email.max' => 'Az e-mail-cím legfeljebb :max karakter lehet.',
            'email.unique' => 'Ezzel az e-mail-címmel már regisztráltak.',
            'password.required' => 'Add meg a jelszavad.',
            'password.string' => 'A jelszó szöveg legyen.',
            'password.confirmed' => 'A jelszó és a megerősítése nem egyezik.',
            'password.min' => 'A jelszónak legalább :min karakterből kell állnia.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        app(CartService::class)->mergeGuestCart($user, $request->session()->get('cart', []));
        $request->session()->forget('cart');

        return redirect(route('dashboard', absolute: false));
    }
}
