<?php 
namespace App\Http\Controllers;

use Illuminte\Http\Request;

class HomeController extends Controller 
{
    public function index()
    {
        return view('Welcome');
    }
}
?>