<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NewsPost extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'image', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('homepage.data.v3');
        });

        static::deleted(static function (): void {
            Cache::forget('homepage.data.v3');
        });
    }
}
