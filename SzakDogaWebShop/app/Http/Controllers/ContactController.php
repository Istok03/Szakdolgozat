<?php
namespace App\Http\Controllers;
use App\Models\ContactMessage;
use Illuminate\Http\Request;



class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'required' => 'A(z) :attribute megadása kötelező.',
            'email.email' => 'Adj meg egy érvényes e-mail-címet.',
            'max' => 'A(z) :attribute legfeljebb :max karakter lehet.',
        ], ['name' => 'név', 'email' => 'e-mail-cím', 'message' => 'üzenet']);


        ContactMessage::create($validated);

        return redirect()->route('contact')->with('success', 'Üzenetedet rögzítettük. A megadott e-mail-címen válaszolunk.');
    }
}
?>
