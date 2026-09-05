<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\DataSource;
use App\Models\Device;
use App\Models\FailedImportRow;
use App\Models\Import;
use App\Models\SpecDefinition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeviceImportService
{
    private const SPEC_MAP = [
        'network' => 'network_technology',
        'network_technology' => 'network_technology',
        'announced' => 'launch_announced',
        'launch_announced' => 'launch_announced',
        'launch_status' => 'launch_status',
        'status' => 'launch_status',
        'dimensions' => 'body_dimensions',
        'body_dimensions' => 'body_dimensions',
        'weight' => 'body_weight',
        'body_weight' => 'body_weight',
        'build' => 'body_build',
        'body_build' => 'body_build',
        'ip_rating' => 'body_ip_rating',
        'water_and_dust_resistance' => 'body_ip_rating',
        'headphone_jack' => 'body_headphone_jack',
        'display' => 'display_type',
        'display_type' => 'display_type',
        'screen_size' => 'display_size',
        'display_size' => 'display_size',
        'resolution' => 'display_resolution',
        'display_resolution' => 'display_resolution',
        'refresh_rate' => 'display_refresh_rate',
        'display_refresh_rate' => 'display_refresh_rate',
        'brightness' => 'display_brightness',
        'display_brightness' => 'display_brightness',
        'hdr' => 'display_hdr',
        'display_hdr' => 'display_hdr',
        'os' => 'platform_os',
        'platform_os' => 'platform_os',
        'chipset' => 'platform_chipset',
        'platform_chipset' => 'platform_chipset',
        'cpu' => 'platform_cpu',
        'platform_cpu' => 'platform_cpu',
        'gpu' => 'platform_gpu',
        'platform_gpu' => 'platform_gpu',
        'ram' => 'memory_ram',
        'memory_ram' => 'memory_ram',
        'storage' => 'memory_storage',
        'internal_storage' => 'memory_storage',
        'memory_storage' => 'memory_storage',
        'storage_type' => 'memory_storage_type',
        'memory_storage_type' => 'memory_storage_type',
        'card_slot' => 'memory_card_slot',
        'memory_card_slot' => 'memory_card_slot',
        'main_camera' => 'main_camera_setup',
        'main_camera_setup' => 'main_camera_setup',
        'main_camera_video' => 'main_camera_video',
        'selfie_camera' => 'selfie_camera_setup',
        'selfie_camera_setup' => 'selfie_camera_setup',
        'selfie_camera_video' => 'selfie_camera_video',
        'loudspeaker' => 'sound_loudspeaker',
        'sound_loudspeaker' => 'sound_loudspeaker',
        'headphone_out' => 'sound_headphone_out',
        'sound_headphone_out' => 'sound_headphone_out',
        'wifi' => 'comms_wifi',
        'comms_wifi' => 'comms_wifi',
        'bluetooth' => 'comms_bluetooth',
        'comms_bluetooth' => 'comms_bluetooth',
        'nfc' => 'comms_nfc',
        'comms_nfc' => 'comms_nfc',
        'usb' => 'comms_usb',
        'comms_usb' => 'comms_usb',
        'sensors' => 'features_sensors',
        'features_sensors' => 'features_sensors',
        'battery' => 'battery_type',
        'battery_type' => 'battery_type',
        'battery_capacity' => 'battery_capacity',
        'charging' => 'battery_charging',
        'battery_charging' => 'battery_charging',
        'wireless_charging' => 'battery_wireless',
        'battery_wireless' => 'battery_wireless',
    ];

    public function import(Import $import): void
    {
        $handle = fopen(storage_path('app/private/' . $import->file_path), 'rb');

        if ($handle === false) {
            throw new \RuntimeException('The import file could not be opened.');
        }

        $dataSource = DataSource::firstOrCreate(
            ['name' => 'CSV import: ' . $import->file_name],
            [
                'type' => 'csv',
                'trust_level' => 3,
                'active' => true,
            ]
        );

        try {
            $headers = fgetcsv($handle);
            if (!$headers) {
                throw new \RuntimeException('The CSV file is empty.');
            }

            $headers = array_map(fn ($header) => Str::lower(trim((string) $header)), $headers);
            $import->update(['total_rows' => $this->countRows($handle)]);
            rewind($handle);
            fgetcsv($handle);

            $processed = 0;
            $successful = 0;

            while (($values = fgetcsv($handle)) !== false) {
                if ($this->rowIsEmpty($values)) {
                    continue;
                }

                $processed++;
                $row = $this->combineRow($headers, $values);

                try {
                    $this->importRow($row, $dataSource);
                    $successful++;
                } catch (\Throwable $e) {
                    FailedImportRow::create([
                        'import_id' => $import->id,
                        'data' => $row,
                        'validation_error' => $e->getMessage(),
                    ]);
                }

                $import->update([
                    'processed_rows' => $processed,
                    'successful_rows' => $successful,
                ]);
            }

            $import->update(['completed_at' => now()]);
        } finally {
            fclose($handle);
        }
    }

    private function importRow(array $row, DataSource $dataSource): void
    {
        $brandName = trim((string) ($row['brand'] ?? ''));
        $name = trim((string) ($row['name'] ?? ''));

        if ($brandName === '' || $name === '') {
            throw new \InvalidArgumentException('Both brand and name are required.');
        }

        $status = trim((string) ($row['status'] ?? 'available')) ?: 'available';
        if (!in_array($status, ['rumored', 'available', 'discontinued'], true)) {
            throw new \InvalidArgumentException('Status must be rumored, available, or discontinued.');
        }

        DB::transaction(function () use ($row, $brandName, $name, $status, $dataSource): void {
            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );

            $requestedSlug = trim((string) ($row['slug'] ?? '')) ?: Str::slug($brand->name . ' ' . $name);
            $device = Device::where('slug', $requestedSlug)->first();

            if (!$device) {
                $device = Device::create([
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'slug' => $this->uniqueSlug($requestedSlug),
                    'release_date' => $this->nullableDate($row['release_date'] ?? null),
                    'status' => $status,
                    'image' => $this->nullableString($row['image'] ?? null),
                    'verification_status' => 'unverified',
                ]);
            } else {
                $device->update([
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'release_date' => $this->nullableDate($row['release_date'] ?? null),
                    'status' => $status,
                    'image' => $this->nullableString($row['image'] ?? null) ?: $device->image,
                ]);
                $device->specs()->delete();
            }

            $device->dataSourceLinks()->updateOrCreate(
                [
                    'data_source_id' => $dataSource->id,
                    'external_id' => $requestedSlug !== '' ? $requestedSlug : $device->slug,
                ],
                [
                    'external_url' => $this->nullableString($row['source_url'] ?? null),
                    'last_seen_at' => now(),
                    'metadata' => ['imported_file' => $dataSource->name],
                ]
            );

            $sortOrder = 1;
            foreach ($row as $key => $value) {
                if (!Str::startsWith($key, 'spec_') || trim((string) $value) === '') {
                    continue;
                }

                $rawKey = Str::snake(Str::after($key, 'spec_'));
                $definitionKey = self::SPEC_MAP[$rawKey] ?? null;
                $definition = $definitionKey
                    ? SpecDefinition::where('key', $definitionKey)->where('active', true)->first()
                    : null;
                $specValue = trim((string) $value);

                $spec = $device->specs()->create([
                    'spec_definition_id' => $definition?->id,
                    'category' => $definition?->category ?? $this->specCategory($rawKey),
                    'spec_key' => $definition?->label ?? Str::headline($rawKey),
                    'spec_value' => $specValue,
                    'numeric_value' => $definition && in_array($definition->value_type, ['integer', 'decimal'], true)
                        ? $this->numericValue($specValue)
                        : null,
                    'boolean_value' => $definition && $definition->value_type === 'boolean'
                        ? $this->booleanValue($specValue)
                        : null,
                    'sort_order' => $sortOrder++,
                ]);

                $spec->sources()->create([
                    'data_source_id' => $dataSource->id,
                    'source_value' => $specValue,
                    'source_url' => $this->nullableString($row['source_url'] ?? null),
                    'verification_status' => 'unverified',
                ]);
            }
        });
    }

    private function numericValue(string $value): ?float
    {
        if (preg_match('/-?\d+(?:\.\d+)?/', str_replace(',', '', $value), $matches) !== 1) {
            return null;
        }

        return (float) $matches[0];
    }

    private function booleanValue(string $value): ?bool
    {
        return match (Str::lower(trim($value))) {
            'yes', 'true', '1', 'supported', 'present' => true,
            'no', 'false', '0', 'not supported', 'absent' => false,
            default => null,
        };
    }

    private function combineRow(array $headers, array $values): array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }
            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return $row;
    }

    private function countRows($handle): int
    {
        $count = 0;
        while (fgetcsv($handle) !== false) {
            $count++;
        }

        return $count;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'device';
        $candidate = $slug;
        $counter = 2;

        while (Device::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter++;
        }

        return $candidate;
    }

    private function nullableDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $date = date_create($value);
        if (!$date) {
            throw new \InvalidArgumentException("Invalid release date: {$value}");
        }

        return $date->format('Y-m-d');
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function specCategory(string $specKey): string
    {
        return match (true) {
            Str::contains($specKey, ['screen', 'display', 'resolution']) => 'Display',
            Str::contains($specKey, ['camera', 'selfie', 'video']) => 'Main Camera',
            Str::contains($specKey, ['battery', 'charging']) => 'Battery',
            Str::contains($specKey, ['ram', 'storage', 'memory', 'card_slot']) => 'Memory',
            Str::contains($specKey, ['chipset', 'cpu', 'gpu', 'os']) => 'Platform',
            Str::contains($specKey, ['wifi', 'bluetooth', 'usb', 'nfc', 'network', 'sim']) => 'Comms',
            default => 'Features',
        };
    }
}
