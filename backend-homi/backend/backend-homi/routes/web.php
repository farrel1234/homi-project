<?php

use App\Http\Controllers\AnnouncementController;

Route::middleware(['auth', 'is_admin'])->group(function () {
    // misalnya prefix admin/dashboard
    Route::resource('announcements', AnnouncementController::class);
});
