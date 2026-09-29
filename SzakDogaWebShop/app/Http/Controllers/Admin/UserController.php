<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // LISTÁZÁS
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // ÚJ FELHASZNÁLÓ ŰRLAP
    public function create()
    {
        return view('admin.users.create');
    }

    // ÚJ FELHASZNÁLÓ MENTÉSE
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'is_admin'  => 'required|boolean',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'is_admin'  => $request->is_admin,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Új felhasználó sikeresen létrehozva.');
    }

    // SZERKESZTŐ ŰRLAP
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // FELHASZNÁLÓ FRISSÍTÉSE
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_admin'  => 'required|boolean',
        ]);

        // Admin ne fokozhassa le saját magát
        if (Auth::id() == $user->id && $request->is_admin == 0) {
            return back()->with('error', 'Nem fokozhatod le saját magad.');
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
        ]);

        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Felhasználó sikeresen frissítve.');
    }

    // FELHASZNÁLÓ TÖRLÉSE
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Admin ne törölhesse saját magát
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Nem törölheted saját magad.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Felhasználó sikeresen törölve.');
    }
}
