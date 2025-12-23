<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_type_id',
        'reporter_name',
        'request_date',
        'place',
        'subject',
        'status',
        'admin_note',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'verified_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
