<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\AuthApiController;

// Auth
Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

// Default route test, buat memastikan api.php kebaca
Route::get('/tes', function () {
    return response()->json(['ok' => true]);
});

// Route untuk pembayaran (API user mobile)
Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    Route::get('/', [PaymentApiController::class, 'index']);
    Route::post('/', [PaymentApiController::class, 'store']);
});

