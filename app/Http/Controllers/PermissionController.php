<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\PermissionRoute;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    public function deletePermissionRole(Request $request)
    {
        try {
            PermissionRole::where('permission_id', $request->permission_id)->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Delete Successfully!!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function assignPermissionRoute()
    {
        $permissions = Permission::all();
        $routes = Route::getRoutes();
        // dd($routes);

        $middlewareGroup = 'isAuthenticated';

        $routeDetails = [];

        foreach ($routes as $route) {
            $middlewares = $route->gatherMiddleware();
            if (in_array($middlewareGroup, $middlewares)) {
                $routeName = $route->getName();
                if ($routeName !== 'dashboard' && $routeName !== 'logout') {
                    $routeDetails[] = [
                        'name' => $route->getName(),
                        'uri' => $route->uri()
                    ];
                }
            }
        }

        $routerPermissions = PermissionRoute::with('permission')->get();

        return view('assign-permission-route.index', compact('permissions', 'routeDetails', 'routerPermissions'));
    }

    public function createPermissionRoute(Request $request)
    {
        try {
            $isExistPermissionToRoute = PermissionRoute::where([
                'permission_id' => $request->permission_id
            ])->first();

            if ($isExistPermissionToRoute) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Permission is already assigned!!!'
                ]);
            }

            PermissionRoute::create([
                'permission_id' => $request->permission_id,
                'router' => $request->route
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission is assigned to selected router!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function updatePermissionRoute(Request $request)
    {
        try {
            $isExistPermission = PermissionRoute::whereNotIn('id', [$request->id])->where([
                'permission_id' => $request->permission_id
            ])->first();

            $isExistRouter = PermissionRoute::whereNotIn('id', [$request->id])->where([
                'router' => $request->route
            ])->first();

            if ($isExistPermission) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Permission is already assigned!!!'
                ]);
            } elseif ($isExistRouter) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Route is already assigned!!!'
                ]);
            }

            PermissionRoute::where('id', $request->id)->update([
                'permission_id' => $request->permission_id,
                'router' => $request->route
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission is updated to selected router!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function deletePermissionRoute(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);

            PermissionRoute::where('id', $request->id)->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Permission is deleted of router!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

}
