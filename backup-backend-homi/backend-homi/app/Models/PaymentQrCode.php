<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentQrCode extends Model
{
    protected $fillable = ['image_path','is_active','updated_by','notes'];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
