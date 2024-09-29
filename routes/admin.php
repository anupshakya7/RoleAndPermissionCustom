<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class,'login'])->name('auth.login');
    Route::post('/', [AuthController::class,'loginSubmit'])->name('auth.login.submit');
});

Route::group(['middleware' => ['isAuthenticated']], function () {
    Route::get('/dashboard', [HomeController::class,'dashboard'])->name('dashboard');
    Route::resource('product', ProductController::class);
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
});
