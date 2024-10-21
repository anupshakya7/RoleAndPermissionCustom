<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function users()
    {
        $users = User::with('role')->where('role_id', '!=', 1)->get();
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('users.index', compact('users', 'roles'));
    }

    public function createUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fullname' => 'required',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|min:6|confirmed',
                'role' => 'required'
            ]);

            User::insert([
                'name' => $validatedData['fullname'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => $validatedData['role']
            ]);
            return response()->json([
                'success' => true,
                'msg' => 'User Created!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
                'fullname' => 'required',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($request->id)
                ],
                'password' => 'nullable',
                'role' => 'required'
            ]);

            $user = User::find($validatedData['id']);
            $oldEmail = $user->email;

            $user->name = $validatedData['fullname'];
            $user->email = $validatedData['email'];
            $user->role_id = $validatedData['role'];

            if (isset($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            if ($oldEmail != $validatedData['email'] || isset($validatedData['password'])) {
                //mail send work

            }

            return response()->json([
                'success' => true,
                'msg' => 'User Updated!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
            ]);

            User::where('id', $validatedData['id'])->delete();

            //Mail send to user

            return response()->json([
                'success' => true,
                'msg' => 'User Deleted!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
