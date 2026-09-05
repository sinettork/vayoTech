<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataSource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'url',
        'trust_level',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'trust_level' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function deviceLinks(): HasMany
    {
        return $this->hasMany(DeviceDataSource::class);
    }

    public function specSources(): HasMany
    {
        return $this->hasMany(DeviceSpecSource::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_data_sources')
            ->withPivot(['external_id', 'external_url', 'last_seen_at', 'metadata'])
            ->withTimestamps();
    }
}
