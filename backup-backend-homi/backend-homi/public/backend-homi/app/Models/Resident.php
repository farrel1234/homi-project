<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $table = 'resident_profiles';

    protected $fillable = [
        'user_id',
        'blok',
        'no_rumah',
        'alamat',
        'is_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // compatibility (kalau controller/view lama pakai house_number/address)
    public function getHouseNumberAttribute() { return $this->no_rumah; }
    public function setHouseNumberAttribute($v) { $this->no_rumah = $v; }

    public function getAddressAttribute() { return $this->alamat; }
    public function setAddressAttribute($v) { $this->alamat = $v; }
}
