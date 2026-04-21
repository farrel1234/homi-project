<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    AuthController,
    DashboardController,
    AnnouncementController,
    ResidentController,
    PaymentController,
    ComplaintController,
    LetterRequestController,
    QuickPaymentValidationController,
    FeeInvoiceController,
    FeeQrController,
    PaymentOcrController,
    PrioritasTunggakanController,
    ServiceRequestController as AdminServiceRequestController,
    AppNotificationController,
    TenantController,
    StaffController,
    TenantRequestController as AdminTenantRequestController
};

/*
|--------------------------------------------------------------------------
| DEFAULT ROUTES (Portal Utama)
|--------------------------------------------------------------------------
| / menampilkan landing page portal
*/
Route::get('/', function () {
    return view('welcome');
})->name('portal');

Route::get('/admin', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Alias default Laravel auth redirect:
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

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

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        /*
        ==========================================
        PRIORITAS TUNGGAKAN (TAMBAHAN BARU)
        ==========================================
        */
        Route::get('/prioritas-tunggakan', [PrioritasTunggakanController::class, 'index'])
            ->name('admin.prioritas-tunggakan');

        // ===================== PENGUMUMAN =====================
        Route::resource('announcements', AnnouncementController::class);

        Route::post(
            'announcements/{announcement}/toggle-active',
            [AnnouncementController::class, 'toggleActive']
        )->name('announcements.toggle-active');

        // ===================== DATA WARGA =====================
        Route::get('residents/import', [ResidentController::class, 'importForm'])
            ->name('residents.import.form');

        Route::post('residents/import', [ResidentController::class, 'importCsv'])
            ->name('residents.import');

        Route::get('residents/template.csv', [ResidentController::class, 'downloadTemplate'])
            ->name('residents.template');

        Route::resource('residents', ResidentController::class)->except('show');

        // ===================== PENGADUAN =====================
        Route::resource('complaints', ComplaintController::class);

        // ===================== LETTER REQUESTS =====================
        Route::resource('letter-requests', LetterRequestController::class)
            ->only(['index', 'show', 'update']);

        Route::post('letter-requests/{letterRequest}/approve', [LetterRequestController::class, 'approve'])
            ->name('letter-requests.approve');

        Route::post('letter-requests/{letterRequest}/reject', [LetterRequestController::class, 'reject'])
            ->name('letter-requests.reject');

        Route::get('letter-requests/{letterRequest}/download', [LetterRequestController::class, 'download'])
            ->name('letter-requests.download');

        // ===================== SERVICE REQUESTS =====================
        Route::resource('service-requests', AdminServiceRequestController::class)
            ->only(['index', 'show', 'update']);

        Route::post('service-requests/{serviceRequest}/approve', [AdminServiceRequestController::class, 'approve'])
            ->name('service-requests.approve');

        Route::get('service-requests/{serviceRequest}/preview', [AdminServiceRequestController::class, 'preview'])
            ->name('service-requests.preview');

        Route::post('service-requests/{serviceRequest}/reject', [AdminServiceRequestController::class, 'reject'])
            ->name('service-requests.reject');

        Route::get('service-requests/{serviceRequest}/download', [AdminServiceRequestController::class, 'download'])
            ->name('service-requests.download');

        // ===================== PEMBAYARAN =====================
        Route::resource('payments', PaymentController::class)->only(['index', 'show']);

        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])
            ->name('payments.approve');

        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])
            ->name('payments.reject');

        Route::post('payments/{payment}/cancel', [PaymentController::class, 'cancel'])
            ->name('payments.cancel');

        Route::post('payments/bulk', [PaymentController::class, 'bulk'])
            ->name('payments.bulk');

        Route::post('payments/{payment}/scan-ocr', [PaymentOcrController::class, 'scan'])
            ->name('payments.scan-ocr');

        Route::post('payments/{id}/quick-approve', [QuickPaymentValidationController::class, 'approve'])
            ->name('payments.quick-approve');

        Route::post('payments/{id}/quick-reject', [QuickPaymentValidationController::class, 'reject'])
            ->name('payments.quick-reject');

        // ===================== FEES =====================
        Route::prefix('fees')->name('admin.fees.')->group(function () {

            Route::get('qr', [FeeQrController::class, 'index'])->name('qr.index');
            Route::post('qr', [FeeQrController::class, 'store'])->name('qr.store');
            Route::post('qr/{id}/activate', [FeeQrController::class, 'activate'])->name('qr.activate');
            Route::delete('qr/{id}', [FeeQrController::class, 'destroy'])->name('qr.destroy');

            Route::get('invoices', [FeeInvoiceController::class, 'index'])->name('invoices.index');
            Route::get('invoices/create', [FeeInvoiceController::class, 'create'])->name('invoices.create');
            Route::post('invoices', [FeeInvoiceController::class, 'store'])->name('invoices.store');
            Route::post('invoices/bulk-destroy', [FeeInvoiceController::class, 'bulkDestroy'])->name('invoices.bulk-destroy');
            Route::delete('invoices/{invoice}', [FeeInvoiceController::class, 'destroy'])->name('invoices.destroy');
        });

        // ===================== NOTIFICATIONS =====================
        Route::prefix('notifications')->name('admin.notifications.')->group(function () {

            Route::get('/', [AppNotificationController::class, 'index'])->name('index');
            Route::get('/create', [AppNotificationController::class, 'create'])->name('create');
            Route::post('/', [AppNotificationController::class, 'store'])->name('store');

            Route::post('/send-risk/{userId}', [AppNotificationController::class, 'sendRiskWarning'])
                ->name('send-risk');
        });

        // ===================== STAFF =====================
        Route::resource('staff', StaffController::class)
            ->names('admin.staff')
            ->parameters(['staff' => 'staff']);

        // ===================== TENANTS =====================
        Route::resource('tenants', TenantController::class);

        Route::post('tenants/{tenant}/migrate', [TenantController::class, 'migrateDatabase'])
            ->name('tenants.migrate');

        Route::get(
            'tenants/{id}/switch',
            [\App\Http\Controllers\Admin\ImpersonateTenantController::class, 'switch']
        )->name('admin.tenants.switch');

        // ===================== TENANT REQUESTS =====================
        Route::resource('tenant-requests', AdminTenantRequestController::class)
            ->only(['index', 'update', 'destroy']);

        Route::get('tenant-requests/{tenantRequest}/approve', [AdminTenantRequestController::class, 'approve'])
            ->name('tenant-requests.approve');
    });
});