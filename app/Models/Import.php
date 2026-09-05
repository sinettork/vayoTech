<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    protected $fillable = [
        'completed_at',
        'file_name',
        'file_path',
        'importer',
        'processed_rows',
        'total_rows',
        'successful_rows',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedImportRow::class);
    }
}
