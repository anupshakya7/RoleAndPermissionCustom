<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class,'login'])->name('auth.login');
    Route::post('/', [AuthController::class,'loginSubmit'])->name('auth.login.submit');
    Route::get('/register', [AuthController::class,'register'])->name('auth.register');
    Route::post('/register', [AuthController::class,'registerSubmit'])->name('auth.register.submit');
});

Route::group(['middleware' => ['isAuthenticated']], function () {
    Route::get('/dashboard', [HomeController::class,'dashboard'])->name('dashboard');
    Route::resource('product', ProductController::class);
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');

    // Manage Roles Routes
    Route::get('/manage-role', [RoleController::class,'manageRole'])->name('manageRole');
    Route::post('/create-role', [RoleController::class,'createRole'])->name('createRole');
    Route::post('/update-role', [RoleController::class,'updateRole'])->name('updateRole');
    Route::post('/delete-role', [RoleController::class,'deleteRole'])->name('deleteRole');

    //Manage Permission Routes
    Route::get('/manage-permission', [PermissionController::class,'managePermission'])->name('managePermission');
    Route::post('/create-permission', [PermissionController::class,'createPermission'])->name('createPermission');
    Route::post('/update-permission', [PermissionController::class,'updatePermission'])->name('updatePermission');
    Route::post('/delete-permission', [PermissionController::class,'deletePermission'])->name('deletePermission');

    //Assign Permission to Role Routes
    Route::get('/assign-permission-role', [PermissionController::class,'assignPermissionRole'])->name('assignPermissionRole');
    Route::post('/create-permission-role', [PermissionController::class,'createPermissionRole'])->name('createPermissionRole');
    Route::post('/update-permission-role', [PermissionController::class,'updatePermissionRole'])->name('updatePermissionRole');
    Route::post('/delete-permission-role', [PermissionController::class,'deletePermissionRole'])->name('deletePermissionRole');

    //Assign Permission to Route
    Route::get('assign-permission-route', [PermissionController::class,'assignPermissionRoute'])->name('assignPermissionRoute');
    Route::post('create-permission-route', [PermissionController::class,'createPermissionRoute'])->name('createPermissionRoute');
    Route::post('update-permission-route', [PermissionController::class,'updatePermissionRoute'])->name('updatePermissionRoute');
    Route::post('delete-permission-route', [PermissionController::class,'deletePermissionRoute'])->name('deletePermissionRoute');

    //Manage Users Route
    Route::get('/users', [UserController::class,'users'])->name('users');
    Route::post('/create-user', [UserController::class,'createUser'])->name('createUser');
    Route::post('/update-user', [UserController::class,'updateUser'])->name('updateUser');
    Route::post('/delete-user', [UserController::class,'deleteUser'])->name('deleteUser');
});
