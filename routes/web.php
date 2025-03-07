<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\EsewaController;
use App\Http\Controllers\User\FonepayController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController;
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
Route::get('/', [HomeController::class,'dashboard'])->name('home');
Route::resource('/product', ProductController::class);

//Checkout Order
Route::post('check-out', [OrderController::class,'checkout'])->name('checkout');

//Esewa Success
Route::any('esewa/success', [EsewaController::class,'success'])->name('esewa.success');

//Esewa Fail
Route::any('esewa/fail', [EsewaController::class,'fail'])->name('esewa.fail');

//Fonepay Payment Verify
Route::get('fonepay', [FonepayController::class,'index']);
Route::get('fonepay/payment/verify', [FonepayController::class,'verifyPayment'])->name('verifyPayment');
