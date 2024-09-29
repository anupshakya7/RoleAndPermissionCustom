<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
        $latestProducts = Product::where('is_active', 1)->latest()->limit(4)->get();

        return view('user.dashboard', compact('latestProducts'));
    }
}
