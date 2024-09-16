<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $userCredentials = $request->only('email', 'password');

        if (Auth::attempt($userCredentials)) {
            return redirect('/dashboard');
        }

        return back()->with('error', 'Email & Password Incorrect');
    }
}
