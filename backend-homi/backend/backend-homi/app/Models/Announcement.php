<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'content',
        'body',
        'image_path',
        'published_at',
        'start_at',
        'end_at',
        'created_by',
        'author_id',
        'is_pinned',
        'is_public',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
        'is_pinned'    => 'boolean',
        'is_public'    => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) return null;
        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
