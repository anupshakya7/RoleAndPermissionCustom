<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class,'login'])->name('auth.login');
    Route::post('/', [AuthController::class,'loginSubmit'])->name('auth.login.submit');
});

Route::group(['middleware' => ['isAuthenticated']], function () {
    Route::get('/dashboard', [HomeController::class,'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
});
