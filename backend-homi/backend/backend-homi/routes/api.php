<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Api\AnnouncementController as ApiAnnouncementController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\AdminResidentController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\ServiceRequestAdminController;
use App\Http\Controllers\Api\FeeTypeController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\FeeInvoiceController;
use App\Http\Controllers\Api\FeePaymentController;
use App\Http\Controllers\Api\FeePaymentReviewController;



Route::get('/ping', function (): JsonResponse {
    return response()->json([
        'message' => 'HOMI backend API is running',
        'status'  => 'ok',
    ]);
});

// Auth Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

// == Lupa Pasword ==
// Forgot Password (Public)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);




// Routes yang butuh login (token Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // == User info & logout ==
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
     // UPDATE PROFIL (GANTI NAMA)
    Route::put('/me', [AuthController::class, 'updateProfile']);
    // ganti password
    Route::post('/change-password', [AuthController::class, 'changePassword']);



    // === Pengaduan Warga ===
    // List semua pengaduan (nanti bisa difilter per user kalau mau)
    Route::get('/complaints', [ComplaintController::class, 'index']);
    // Kirim pengaduan baru + upload foto
    Route::post('/complaints', [ComplaintController::class, 'store']);
    // Lihat detail pengaduan tertentu
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);



    //== Pengumuman Warga ==
    //lihat pengumuman
    Route::get('/announcements', [ApiAnnouncementController::class, 'index']);
    Route::get('/announcements/{id}', [ApiAnnouncementController::class, 'show']);


    // == Laporan Warga == 
    Route::get('/request-types', [ServiceRequestController::class, 'types']);
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    Route::get('/service-requests', [ServiceRequestController::class, 'index']);
    Route::get('/service-requests/{id}', [ServiceRequestController::class, 'show']);

    // == Pembayara iuran ==
    Route::get('/fees/invoices', [FeeInvoiceController::class, 'residentIndex']);
    Route::post('/fees/invoices/{invoiceId}/pay', [FeePaymentController::class, 'pay']);
    Route::get('/fees/history', [FeePaymentController::class, 'history']);
    Route::get('/payment-qr-codes/active', [QrCodeController::class, 'active']);



    // === Direktori Warga (warga lihat) ===
    Route::get('/directory', [DirectoryController::class, 'index']);

    // == Admin ==
    Route::middleware('is_admin')->group(function () {
        // buat pengumuman warga
        Route::post('/announcements', [ApiAnnouncementController::class, 'store']);

        // admin mengelola data warga
        Route::get('/admin/residents', [AdminResidentController::class, 'index']);
        Route::get('/admin/residents/{user}', [AdminResidentController::class, 'show']);

        Route::put('/admin/residents/{user}', [AdminResidentController::class, 'upsertAddress']);
        Route::patch('/admin/residents/{user}/visibility', [AdminResidentController::class, 'updateVisibility']);

        Route::delete('/admin/residents/{user}/profile', [AdminResidentController::class, 'destroyProfile']);

        // admin mengelola data pengajuan warga
        Route::get('/admin/service-requests', [ServiceRequestAdminController::class, 'index']);
        Route::get('/admin/service-requests/{id}', [ServiceRequestAdminController::class, 'show']);
        Route::patch('/admin/service-requests/{id}/status', [ServiceRequestAdminController::class, 'updateStatus']);

        // Admin mengirim tagihan warga
        Route::get('/admin/qr-codes', [QrCodeController::class, 'adminIndex']);
        Route::post('/admin/qr-codes', [QrCodeController::class, 'adminStore']);

        Route::post('/admin/fee-invoices', [FeeInvoiceController::class, 'adminCreate']);
        Route::get('/admin/fee-invoices', [FeeInvoiceController::class, 'adminIndex']);

        Route::get('/admin/fee-payments/pending', [FeePaymentReviewController::class, 'pending']);
        Route::post('/admin/fee-payments/{id}/approve', [FeePaymentReviewController::class, 'approve']);
        Route::post('/admin/fee-payments/{id}/reject', [FeePaymentReviewController::class, 'reject']);

        Route::post('/payment-qr-codes', [QrCodeController::class, 'store']);

        
    });

});
