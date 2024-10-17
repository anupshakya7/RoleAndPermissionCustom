<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function manageRole()
    {
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        return view('manage-role.index', compact('roles'));
    }

    public function createRole(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required|unique:roles,name|max:255'
            ]);

            $role = Role::create([
                'name' => $validatedData['role']
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Role Created!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
             'success' => false,
             'msg' => $e->getMessage()
            ]);
        }
    }


    public function updateRole(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required',Rule::unique('roles', 'name')->ignore($request->role_id),'max:255'
            ]);

            Role::where('id', $request->role_id)->update([
                'name' => $validatedData['role']
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Role Updated!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
             'success' => false,
             'msg' => $e->getMessage()
            ]);
        }
    }

    public function deleteRole(Request $request)
    {
        try {
            Role::where('id', $request->role)->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Role Deleted!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
