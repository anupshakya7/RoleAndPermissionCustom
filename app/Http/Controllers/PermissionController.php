<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
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

    public function assignPermissionRole()
    {
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        $permissions = Permission::all();
        $permissionsWithRoles = Permission::with('roles')->whereHas('roles')->get();

        return view('assign-permission-role.index', compact('roles', 'permissions', 'permissionsWithRoles'));
    }

    public function createPermissionRole(Request $request)
    {
        try {
            $isExistPermissionToRole = PermissionRole::where([
                'permission_id' => $request->permission_id,
                'role_id' => $request->role_id,
            ])->first();

            if ($isExistPermissionToRole) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Permission is Already assigned to selected Role!'
                ]);
            }

            PermissionRole::create([
                'permission_id' => $request->permission_id,
                'role_id' => $request->role_id,
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission is assigned to selected Role!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function updatePermissionRole(Request $request)
    {
        try {
            $permission = Permission::find($request->permission);
            $permission->roles()->sync($request->roles);

            return response()->json([
                'success' => true,
                'msg' => 'Permission is assigned to selected Role!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
