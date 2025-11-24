<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $table = 'residents';

    protected $fillable = [
        'user_id',
        'house_number',
        'address',
        'id_number',
        'family_head',
        'other_info',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
