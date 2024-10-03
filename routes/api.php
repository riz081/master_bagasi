<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Voucher\VoucherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route untuk autentikasi
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');   

// Route untuk produk
Route::middleware('auth:api')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::post('products/{id}/vouchers', [ProductController::class, 'attachVoucher']);
    Route::get('products/{id}/vouchers', [ProductController::class, 'getVouchers']);
});

// Route untuk voucher
Route::middleware('auth:api')->group(function () {
    Route::get('vouchers', [VoucherController::class, 'index']);
    Route::get('vouchers/{id}', [VoucherController::class, 'show']);
    Route::post('vouchers', [VoucherController::class, 'store']);
    Route::post('vouchers/redeem', [VoucherController::class, 'redeem']);
    Route::put('vouchers/{id}/status', [VoucherController::class, 'updateStatus']);
    Route::delete('vouchers', [VoucherController::class, 'deleteAll']);
});
