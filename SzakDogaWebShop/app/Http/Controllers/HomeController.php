<?php 
namespace App\Http\Controllers;

use Illuminte\Http\Request;
use App\Models\Review;

class HomeController extends Controller 
{
    public function index()
    {
       
        return view('home');
    }
}
?>