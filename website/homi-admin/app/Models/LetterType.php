<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'template_html',
        'required_fields',
    ];

    protected $casts = [
        'required_fields' => 'array',
    ];

    public function requests()
    {
        return $this->hasMany(LetterRequest::class);
    }
}
