<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSpecSource extends Model
{
    protected $fillable = [
        'device_spec_id',
        'data_source_id',
        'source_value',
        'source_url',
        'verification_status',
        'verified_by',
        'verified_at',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function deviceSpec(): BelongsTo
    {
        return $this->belongsTo(DeviceSpec::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
