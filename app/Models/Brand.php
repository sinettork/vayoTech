<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'brand_domain',
        'logo',
        'description',
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

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function getBrandfetchLogoUrlAttribute(): ?string
    {
        $clientId = config('services.brandfetch.client_id');

        if (!$clientId || !$this->brand_domain) {
            return null;
        }

        $domain = preg_replace('#^https?://#i', '', trim($this->brand_domain));
        $domain = preg_replace('#^www\.#i', '', $domain);
        $domain = trim($domain, '/');

        return 'https://cdn.brandfetch.io/domain/'
            . rawurlencode(strtolower($domain))
            . '/w/96/h/96?c='
            . rawurlencode($clientId);
    }
}
