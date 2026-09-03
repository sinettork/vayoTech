<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'description'];

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('homepage.data.v3');
        });

        static::deleted(static function (): void {
            Cache::forget('homepage.data.v3');
        });
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
