<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceSpec extends Model
{
    protected $fillable = ['device_id', 'category', 'spec_key', 'spec_value', 'sort_order'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(DeviceSpecSource::class);
    }
}
