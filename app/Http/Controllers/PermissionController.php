<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function managePermission()
    {
        $permissions = Permission::all();
        return view('manage-permission.index', compact('permissions'));
    }

    public function createPermission(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'permission' => 'required|unique:permissions,name|max:255'
            ]);

            $permission = Permission::create([
                'name' => $validatedData['permission']
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission Created!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
             'success' => false,
             'msg' => $e->getMessage()
            ]);
        }
    }


    public function updatePermission(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'permission' => 'required',Rule::unique('permissions', 'name')->ignore($request->permission_id),'max:255'
            ]);

            Permission::where('id', $request->permission_id)->update([
                'name' => $validatedData['permission']
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission Updated!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
             'success' => false,
             'msg' => $e->getMessage()
            ]);
        }
    }

    public function deletePermission(Request $request)
    {
        try {
            Permission::where('id', $request->permission)->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Permission Deleted!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
