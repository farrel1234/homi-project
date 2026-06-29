<?php

use Illuminate\Support\Facades\Route;

// Pakai namespace Admin biar tidak tabrakan dengan Controller API mobile
use App\Http\Controllers\Admin\{
    AuthController,
    DashboardController,
    AnnouncementController,
    ResidentController,
    PaymentController,
    ComplaintController,
    LetterRequestController,
    QuickPaymentValidationController
};

/*
|--------------------------------------------------------------------------
| Web Routes (HOMI Admin)
|--------------------------------------------------------------------------
*/

// Alias default Laravel auth redirect:
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');

// ======================= ADMIN =======================
Route::prefix('admin')->group(function () {

    // -------- GUEST --------
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    });

    // -------- AUTH --------
    Route::middleware('auth')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Pengumuman
        Route::resource('announcements', AnnouncementController::class);

        // Data warga
        Route::resource('residents', ResidentController::class)->except('show');

        // Pembayaran (admin)
        Route::resource('payments', PaymentController::class)->only(['index', 'show']);
        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{payment}/reject',  [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('payments/{payment}/cancel',  [PaymentController::class, 'cancel'])->name('payments.cancel');
        Route::post('payments/bulk',              [PaymentController::class, 'bulk'])->name('payments.bulk');

        Route::post('payments/{id}/quick-approve', [QuickPaymentValidationController::class, 'approve'])->name('payments.quick-approve');
        Route::post('payments/{id}/quick-reject',  [QuickPaymentValidationController::class, 'reject'])->name('payments.quick-reject');

        // Pengaduan
        Route::resource('complaints', ComplaintController::class);

        // Surat
        Route::resource('letter-requests', LetterRequestController::class)->only(['index', 'show', 'update']);
        Route::post('letter-requests/{letterRequest}/approve', [LetterRequestController::class, 'approve'])->name('letter-requests.approve');
        Route::post('letter-requests/{letterRequest}/reject',  [LetterRequestController::class, 'reject'])->name('letter-requests.reject');
        Route::get('letter-requests/{letterRequest}/download', [LetterRequestController::class, 'download'])->name('letter-requests.download');
    });
});
