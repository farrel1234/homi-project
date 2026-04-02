<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * Tenant registry disimpan di master DB (central).
     */
    protected $connection = 'central';

    protected $fillable = [
        'name',
        'code',
        'registration_code',
        'domain',
        'db_driver',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'is_active',
    ];

    protected $casts = [
        'db_port' => 'integer',
        'is_active' => 'boolean',
    ];
}
