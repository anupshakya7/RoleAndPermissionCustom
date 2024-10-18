<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userCredentials = $request->only('email', 'password');

        if (Auth::attempt($userCredentials)) {
            return redirect()->route('dashboard')->with('success', 'Login Successfully!!!');
        }

        return back()->with('error', 'Email & Password Incorrect');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerSubmit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fullname' => 'required',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|min:6|confirmed'
            ]);

            $role = Role::where('name', 'User')->first();

            User::insert([
                'name' => $validatedData['fullname'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => $role ? $role->id : 0
            ]);
            return back()->with('success', 'Registration Successfully!!!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->session()->flush();
            Auth::logout();

            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
