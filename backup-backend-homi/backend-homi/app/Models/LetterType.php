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
        'required_json' => 'array', // ["nama","nik",...]
    ];

    public function requiredFields(): array
    {
        $r = $this->required_json;

        // format: ["nama","nik",...]
        if (is_array($r) && isset($r[0]) && is_string($r[0])) {
            return $r;
        }

        // fallback kalau suatu saat jadi object: {"fields":[{"name":"nama"},...]}
        if (is_array($r) && isset($r['fields']) && is_array($r['fields'])) {
            $out = [];
            foreach ($r['fields'] as $f) {
                $name = $f['name'] ?? null;
                if ($name) $out[] = $name;
            }
            return $out;
        }

        return [];
    }
}
