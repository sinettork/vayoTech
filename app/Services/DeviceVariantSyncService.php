<?php

namespace App\Services;

use App\Models\Device;

class DeviceVariantSyncService
{
    /**
     * @param  array<int, array{ram: string, storage: string, storage_type?: string|null, model_code?: string|null, market?: string|null, price?: string|null, currency?: string|null, is_default?: bool, sort_order?: int}>  $variants
     */
    public function sync(Device $device, array $variants): void
    {
        $device->variants()->delete();

        foreach (array_values($variants) as $index => $variant) {
            $device->variants()->create([
                'ram' => $variant['ram'],
                'storage' => $variant['storage'],
                'storage_type' => $variant['storage_type'] ?? null,
                'model_code' => $variant['model_code'] ?? null,
                'market' => $variant['market'] ?? null,
                'price' => ($variant['price'] ?? '') !== '' ? $variant['price'] : null,
                'currency' => $variant['currency'] ?? 'USD',
                'is_default' => (bool) ($variant['is_default'] ?? false),
                'sort_order' => $index,
            ]);
        }
    }
}
