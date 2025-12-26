<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'is_admin'=>'required|boolean',
        ]);
        
        \App\Models\User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'is_admin' => $request->is_admin,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Új felhasználó sikeresen létrehozva');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('admin.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|string|max:255',
            'is_admin' => 'required|boolean', 
        ]);

        $user -> name = $request->name;
        $user -> email = $request->email;
        $user -> is_admin = $request->is_admin;
        $user -> save();

        return redirect->route('admin.users.index')
            ->with('success', 'Felhasználó sikeresen frissítve');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = \App\Models\Users::findOrFail($id);

        if(auth()->id()==$user->id){
            return redirect()->route('admin.users.index')
                ->with('error', 'Nem törölheted saját magad.');
        }
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Felhasználó sikeresen törölve.')
    
    
    }


}
