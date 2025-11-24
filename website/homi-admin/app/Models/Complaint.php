<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function assigned() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getStatusLabelAttribute() {
        return match ($this->status) {
            'submitted' => 'Diajukan',
            'investigating' => 'Diselidiki',
            'resolved' => 'Selesai',
            'dismissed' => 'Ditolak',
            default => '-'
        };
    }
}
