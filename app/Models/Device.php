<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Device extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['brand_id', 'name', 'slug', 'release_date', 'image', 'status'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function specs()
    {
        return $this->hasMany(DeviceSpec::class);
    }

    public function groupedSpecs()
    {
        return $this->specs()->orderBy('sort_order')->get()->groupBy('category');
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