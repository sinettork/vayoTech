<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Device;
use App\Models\FailedImportRow;
use App\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeviceImportService
{
    private const DEVICE_COLUMNS = [
        'brand',
        'name',
        'slug',
        'release_date',
        'status',
        'image',
    ];

    public function import(Import $import): void
    {
        $handle = fopen(storage_path('app/private/' . $import->file_path), 'rb');

        if ($handle === false) {
            throw new \RuntimeException('The import file could not be opened.');
        }

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
                $processed++;
                $row = $this->combineRow($headers, $values);

                try {
                    $this->importRow($row);
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

    private function importRow(array $row): void
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

        $device = DB::transaction(function () use ($row, $brandName, $name, $status): Device {
            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );

            $slug = trim((string) ($row['slug'] ?? '')) ?: Str::slug($brand->name . ' ' . $name);
            $device = Device::where('slug', $slug)->first();

            if (!$device) {
                $device = Device::create([
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'slug' => $this->uniqueSlug($slug),
                    'release_date' => $this->nullableDate($row['release_date'] ?? null),
                    'status' => $status,
                    'image' => $this->nullableString($row['image'] ?? null),
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

            $sortOrder = 1;
            foreach ($row as $key => $value) {
                if (!Str::startsWith($key, 'spec_') || trim((string) $value) === '') {
                    continue;
                }

                $specKey = Str::headline(Str::after($key, 'spec_'));
                $device->specs()->create([
                    'category' => $this->specCategory($specKey),
                    'spec_key' => $specKey,
                    'spec_value' => trim((string) $value),
                    'sort_order' => $sortOrder++,
                ]);
            }

            return $device;
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
