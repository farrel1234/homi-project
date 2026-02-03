<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
