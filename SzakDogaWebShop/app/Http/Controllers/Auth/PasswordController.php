<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Add meg a jelenlegi jelszavadat.',
            'current_password.current_password' => 'A jelenlegi jelszó nem megfelelő.',
            'password.required' => 'Add meg az új jelszavadat.',
            'password.string' => 'A jelszó szöveges érték legyen.',
            'password.min' => 'Az új jelszó legalább :min karakter hosszú legyen.',
            'password.confirmed' => 'Az új jelszó és a megerősítése nem egyezik.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
