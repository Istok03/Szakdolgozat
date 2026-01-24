<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;



class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);


        return redirect()->route('contact')->with('success', 'Üzenet sikeresen elküldve!');
    }
}
?>