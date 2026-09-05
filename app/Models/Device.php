<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Device extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['brand_id', 'name', 'slug', 'release_date', 'image', 'status', 'verification_status'];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(DeviceSpec::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DeviceVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function groupedSpecs()
    {
        return $this->specs()->orderBy('sort_order')->get()->groupBy('category');
    }

    public function dataSourceLinks(): HasMany
    {
        return $this->hasMany(DeviceDataSource::class);
    }

    public function dataSources(): BelongsToMany
    {
        return $this->belongsToMany(DataSource::class, 'device_data_sources')
            ->withPivot(['external_id', 'external_url', 'last_seen_at', 'metadata'])
            ->withTimestamps();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('hero')
            ->fit(Fit::Contain, 600, 600)
            ->nonQueued();
    }
}
