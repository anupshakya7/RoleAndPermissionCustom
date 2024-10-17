<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class,'login'])->name('auth.login');
    Route::post('/', [AuthController::class,'loginSubmit'])->name('auth.login.submit');
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
    Route::post('/update-permission', [RoleController::class,'updatePermission'])->name('updatePermission');
    Route::post('/delete-permission', [RoleController::class,'deletePermission'])->name('deletePermission');
});
