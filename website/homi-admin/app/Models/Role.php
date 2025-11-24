<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false; // di dump hanya ada created_at (opsional)
    protected $fillable = ['name', 'description'];
}
