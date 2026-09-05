<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\DataSource;
use App\Models\Device;
use App\Models\FailedImportRow;
use App\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeviceImportService
{
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
            $existingDevice = Device::where('slug', $requestedSlug)->first();
            $device = $existingDevice;

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
                    'metadata' => [
                        'imported_file' => $dataSource->name,
                    ],
                ]
            );

            $sortOrder = 1;
            foreach ($row as $key => $value) {
                if (!Str::startsWith($key, 'spec_') || trim((string) $value) === '') {
                    continue;
                }

                $specKey = Str::headline(Str::after($key, 'spec_'));
                $spec = $device->specs()->create([
                    'category' => $this->specCategory($specKey),
                    'spec_key' => $specKey,
                    'spec_value' => trim((string) $value),
                    'sort_order' => $sortOrder++,
                ]);

                $spec->sources()->create([
                    'data_source_id' => $dataSource->id,
                    'source_value' => trim((string) $value),
                    'source_url' => $this->nullableString($row['source_url'] ?? null),
                    'verification_status' => 'unverified',
                ]);
            }
        });
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
        $key = Str::lower($specKey);

        return match (true) {
            Str::contains($key, ['screen', 'display', 'resolution']) => 'Display',
            Str::contains($key, ['camera', 'selfie', 'video']) => 'Camera',
            Str::contains($key, ['battery', 'charging']) => 'Battery',
            Str::contains($key, ['ram', 'storage', 'memory', 'card slot']) => 'Memory',
            Str::contains($key, ['chipset', 'cpu', 'gpu', 'os']) => 'Platform',
            Str::contains($key, ['wifi', 'bluetooth', 'usb', 'nfc', 'network', 'sim']) => 'Connectivity',
            default => 'General',
        };
    }
}
