<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';

    protected $fillable = [
        'name',
        'description',
        'template_html',
        'required_json',
    ];

    protected $casts = [
        'required_json' => 'array',
    ];

    /**
     * Dipakai oleh Admin\LetterRequestController:
     * $type->fields
     */
    public function getFieldsAttribute(): array
    {
        return $this->required_json ?? [];
    }
}
