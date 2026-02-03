<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;

use App\Http\Controllers\Api\AnnouncementController as ApiAnnouncementController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\AdminResidentController;
use App\Http\Controllers\Api\ResidentProfileController;

use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\ServiceRequestAdminController;

use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\FeeInvoiceController;
use App\Http\Controllers\Api\FeePaymentController;
use App\Http\Controllers\Api\FeePaymentReviewController;

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| API Ping
|--------------------------------------------------------------------------
*/
Route::get('/ping', function (): JsonResponse {
    return response()->json([
        'message' => 'HOMI backend API is running',
        'status'  => 'ok',
    ]);
});

/*
|--------------------------------------------------------------------------
| DEBUG PHP (sementara untuk cek runtime)
| URL: GET /api/__debug/php
|--------------------------------------------------------------------------
| Ini untuk memastikan request dari Android itu diproses oleh PHP yang mana
| (XAMPP atau Laragon) + CA file yang kebaca apa.
*/
Route::get('/__debug/php', function (): JsonResponse {
    return response()->json([
        'php_binary'       => PHP_BINARY,
        'php_version'      => PHP_VERSION,
        'loaded_ini'       => php_ini_loaded_file(),
        'curl_cainfo'      => ini_get('curl.cainfo'),
        'openssl_cafile'   => ini_get('openssl.cafile'),
        'curl_cainfo_exist' => file_exists((string) ini_get('curl.cainfo')),
        'openssl_cafile_exist' => file_exists((string) ini_get('openssl.cafile')),
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('login', [AuthController::class, 'login']);

// Google login (versi controller API)
Route::post('auth/google', [GoogleAuthController::class, 'login']);

// Forgot password
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-reset-otp', [AuthController::class, 'verifyResetOtp']);

// Google login (versi AuthController yang dipakai Android kamu sekarang)
Route::post('login-google', [AuthController::class, 'loginGoogle']);

/*
|--------------------------------------------------------------------------
| AUTH (PROTECTED) - WARGA
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // =========================
    // SESSION / PROFILE
    // =========================
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // =========================
    // WARGA ISI ALAMAT SENDIRI
    // =========================
    Route::get('me/resident-profile', [ResidentProfileController::class, 'show']);
    Route::put('me/resident-profile', [ResidentProfileController::class, 'upsert']);

    // =========================
    // COMPLAINTS
    // =========================
    Route::get('complaints', [ComplaintController::class, 'index']);
    Route::post('complaints', [ComplaintController::class, 'store']);
    Route::get('complaints/{complaint}', [ComplaintController::class, 'show']);

    // =========================
    // ANNOUNCEMENTS (WARGA)
    // =========================
    Route::get('announcements', [ApiAnnouncementController::class, 'index']);
    Route::get('announcements/{id}', [ApiAnnouncementController::class, 'show']);

    // =========================
    // DIREKTORI WARGA
    // =========================
    Route::get('directory', [DirectoryController::class, 'index']);

    // =========================
    // SURAT / PENGAJUAN
    // =========================
    Route::get('request-types', [ServiceRequestController::class, 'types']);
    Route::post('service-requests', [ServiceRequestController::class, 'store']);
    Route::get('service-requests', [ServiceRequestController::class, 'index']);
    Route::get('service-requests/{id}', [ServiceRequestController::class, 'show']);
    Route::get('service-requests/{id}/download', [ServiceRequestController::class, 'downloadPdf']);

    // =========================
    // PEMBAYARAN IURAN
    // =========================
    Route::get('fees/invoices', [FeeInvoiceController::class, 'residentIndex']);
    Route::post('fees/invoices/{invoiceId}/pay', [FeePaymentController::class, 'pay']);
    Route::get('fees/history', [FeePaymentController::class, 'history']);
    Route::get('payment-qr-codes/active', [QrCodeController::class, 'active']);

    // =========================
    // NOTIFICATIONS (WARGA)
    // =========================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markRead']);
        Route::post('/read-all', [NotificationController::class, 'readAll']);
    });

    // =========================
    // ADMIN ONLY (diprefix biar jelas)
    // =========================
    Route::prefix('admin')->middleware('is_admin')->group(function () {

        // ADMIN ANNOUNCEMENT (API)
        Route::post('announcements', [ApiAnnouncementController::class, 'store']);

        // ADMIN RESIDENT
        Route::get('residents', [AdminResidentController::class, 'index']);
        Route::get('residents/{user}', [AdminResidentController::class, 'show']);
        Route::put('residents/{user}', [AdminResidentController::class, 'upsertAddress']);
        Route::patch('residents/{user}/visibility', [AdminResidentController::class, 'updateVisibility']);
        Route::delete('residents/{user}/profile', [AdminResidentController::class, 'destroyProfile']);
        Route::post('residents/import', [AdminResidentController::class, 'importCsv']);

        // ADMIN SERVICE REQUEST
        Route::get('service-requests', [ServiceRequestAdminController::class, 'index']);
        Route::get('service-requests/{id}', [ServiceRequestAdminController::class, 'show']);
        Route::patch('service-requests/{id}/status', [ServiceRequestAdminController::class, 'updateStatus']);

        // ADMIN QR
        Route::get('qr-codes', [QrCodeController::class, 'adminIndex']);
        Route::post('qr-codes', [QrCodeController::class, 'adminStore']);

        // ADMIN REVIEW PAYMENT
        Route::get('fee-payments/pending', [FeePaymentReviewController::class, 'pending']);
        Route::post('fee-payments/{id}/approve', [FeePaymentReviewController::class, 'approve']);
        Route::post('fee-payments/{id}/reject', [FeePaymentReviewController::class, 'reject']);
    });
});
