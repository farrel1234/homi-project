<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\LetterRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Arahkan root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ======================= AUTH (GUEST) =======================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ======================= AUTH (LOGGED IN) =======================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ======================= PENGUMUMAN =======================
    // Ini otomatis bikin:
    // announcements.index, announcements.create, announcements.store,
    // announcements.edit, announcements.update, announcements.destroy
    Route::resource('announcements', AnnouncementController::class);

    // ======================= DATA WARGA =======================
    Route::resource('residents', ResidentController::class)->except('show');

    // Single approve / reject
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])
        ->name('payments.approve');

    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])
        ->name('payments.reject');

    // Upload / delete bukti pembayaran
    Route::post('payments/{payment}/proof', [PaymentController::class, 'uploadProof'])
        ->name('payments.proof.upload');

    Route::delete('payments/{payment}/proof', [PaymentController::class, 'removeProof'])
        ->name('payments.proof.remove');

    // Bulk approve / reject
    Route::post('payments/bulk', [PaymentController::class, 'bulk'])
        ->name('payments.bulk');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::resource('payments', PaymentController::class)->only(['index', 'show']);

Route::resource('complaints', ComplaintController::class);

// List & detail
Route::resource('payments', PaymentController::class)->only(['index', 'show']);

// Approve / cancel
Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])
    ->name('payments.approve');

Route::post('payments/{payment}/cancel', [PaymentController::class, 'cancel'])
    ->name('payments.cancel');

// Admin kelola pengajuan surat
Route::resource('letter-requests', LetterRequestController::class)
    ->only(['index', 'show', 'update']);

// Approve + generate PDF
Route::post('letter-requests/{letterRequest}/approve', [LetterRequestController::class, 'approve'])
    ->name('letter-requests.approve');

// Tolak
Route::post('letter-requests/{letterRequest}/reject', [LetterRequestController::class, 'reject'])
    ->name('letter-requests.reject');

// Download PDF
Route::get('letter-requests/{letterRequest}/download', [LetterRequestController::class, 'download'])
    ->name('letter-requests.download');

