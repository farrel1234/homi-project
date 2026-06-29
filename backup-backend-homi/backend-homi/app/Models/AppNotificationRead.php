<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotificationRead extends Model
{
    protected $table = 'app_notification_reads';

    protected $fillable = [
        'user_id',
        'notification_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
