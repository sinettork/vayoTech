<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceSpec extends Model
{
    protected $fillable = [
        'device_id',
        'spec_definition_id',
        'category',
        'spec_key',
        'spec_value',
        'numeric_value',
        'boolean_value',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:3',
            'boolean_value' => 'boolean',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(SpecDefinition::class, 'spec_definition_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(DeviceSpecSource::class);
    }
}
