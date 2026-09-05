<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecDefinition extends Model
{
    protected $fillable = [
        'category',
        'key',
        'label',
        'value_type',
        'unit',
        'filterable',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'filterable' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function specs(): HasMany
    {
        return $this->hasMany(DeviceSpec::class);
    }
}
