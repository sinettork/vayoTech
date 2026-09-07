<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\DataSource;
use App\Models\Device;
use App\Models\DeviceSpec;
use App\Models\DeviceVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MobileDatasetSeeder extends Seeder
{
    /**
     * Imports the public Mobiles Dataset (2025) as normalized devices + variants.
     *
     * The upstream dataset contains roughly 930 rows and includes RAM/storage
     * configurations. The existing VayoTech seed contributes the first 100
     * researched models; this importer adds the public dataset on top, giving
     * the local database 1,000+ phone configuration/spec records without
     * duplicating RAM/storage rows already present.
     */
    private const SOURCE_URL = 'https://gist.githubusercontent.com/SakethGannavarapu/e1120a28b01239125f23162e4e3f15b5/raw/8c99a08d4e4e83a5755f66f202156e338f0e2bf4/Mobiles%20Dataset%20%282025%29.csv';

    public function run(): void
    {
        $source = DataSource::updateOrCreate(
            ['name' => 'Mobiles Dataset 2025 — public research dataset'],
            [
                'type' => 'external',
                'url' => self::SOURCE_URL,
                'trust_level' => 2,
                'active' => true,
            ]
        );

        $response = Http::timeout(90)->retry(2, 500)->get(self::SOURCE_URL);

        if ($response->failed()) {
            throw new \RuntimeException('Unable to download the mobile dataset: HTTP '.$response->status());
        }

        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $response->body());
        $lines = preg_split('/\r\n|\n|\r/', trim($csv));
        $header = null;
        $imported = 0;
        $variants = 0;

        $brandAliases = [
            'POCO' => 'Poco',
            'POCO ' => 'Poco',
            'ASUS' => 'Asus',
            'IQOO' => 'iQOO',
            'iQOO' => 'iQOO',
            'Oneplus' => 'OnePlus',
            'ONEPLUS' => 'OnePlus',
        ];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $row = str_getcsv($line);
            if ($header === null) {
                $header = array_map(static fn ($value) => trim((string) $value), $row);
                continue;
            }

            if (count($row) < 15) {
                continue;
            }

            $data = array_combine($header, array_pad($row, count($header), null));
            if (!is_array($data)) {
                continue;
            }

            $brandName = trim((string) ($data['Company Name'] ?? ''));
            $modelWithVariant = trim((string) ($data['Model Name'] ?? ''));
            $screen = $this->number($data['Screen Size'] ?? null);

            if ($brandName === '' || $modelWithVariant === '' || $screen === null) {
                continue;
            }

            $brandName = $brandAliases[$brandName] ?? $brandName;

            // The source also contains tablets. VayoTech's current catalogue is phone-first.
            if ($screen > 7.1 || preg_match('/\b(pad|tablet)\b/i', $modelWithVariant)) {
                continue;
            }

            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );

            [$deviceName, $storage] = $this->splitModelAndStorage($modelWithVariant);
            $slug = Str::slug($brandName.' '.$deviceName);

            $device = Device::updateOrCreate(
                ['slug' => $slug],
                [
                    'brand_id' => $brand->id,
                    'name' => $deviceName,
                    'release_date' => $this->releaseDate($data['Launched Year'] ?? null),
                    'status' => 'available',
                ]
            );

            $ram = trim((string) ($data['RAM'] ?? '')) ?: 'Unknown';
            $storage = $storage ?: 'Unknown';
            $price = $this->number($data['Launched Price (USA)'] ?? null);

            DeviceVariant::updateOrCreate(
                [
                    'device_id' => $device->id,
                    'ram' => $ram,
                    'storage' => $storage,
                    'model_code' => null,
                    'market' => 'global',
                ],
                [
                    'storage_type' => $this->storageType($brandName),
                    'price' => $price,
                    'currency' => 'USD',
                    'is_default' => $device->variants()->count() === 0,
                    'sort_order' => $device->variants()->count(),
                ]
            );
            $variants++;

            $specs = [
                ['category' => 'Display', 'key' => 'Screen Size', 'value' => $this->clean($data['Screen Size'] ?? null)],
                ['category' => 'Performance', 'key' => 'Processor', 'value' => $this->clean($data['Processor'] ?? null)],
                ['category' => 'Memory', 'key' => 'RAM', 'value' => $ram],
                ['category' => 'Camera', 'key' => 'Front Camera', 'value' => $this->clean($data['Front Camera'] ?? null)],
                ['category' => 'Camera', 'key' => 'Back Camera', 'value' => $this->clean($data['Back Camera'] ?? null)],
                ['category' => 'Battery', 'key' => 'Battery Capacity', 'value' => $this->clean($data['Battery Capacity'] ?? null)],
                ['category' => 'Design', 'key' => 'Weight', 'value' => $this->clean($data['Mobile Weight'] ?? null)],
                ['category' => 'Launch', 'key' => 'Launched Year', 'value' => $this->clean($data['Launched Year'] ?? null)],
                ['category' => 'Price', 'key' => 'Launch Price (USD)', 'value' => $this->clean($data['Launched Price (USA)'] ?? null)],
            ];

            foreach ($specs as $index => $spec) {
                if ($spec['value'] === '') {
                    continue;
                }

                DeviceSpec::updateOrCreate(
                    [
                        'device_id' => $device->id,
                        'category' => $spec['category'],
                        'spec_key' => $spec['key'],
                    ],
                    [
                        'spec_value' => $spec['value'],
                        'sort_order' => $index,
                    ]
                );
            }

            $imported++;
        }

        $this->command?->info("Mobile dataset import complete: {$imported} phone rows processed, {$variants} RAM/storage variants upserted.");
    }

    private function splitModelAndStorage(string $model): array
    {
        $storage = null;

        if (preg_match('/\s+(\d+(?:\.\d+)?)\s*(GB|TB)\s*$/i', $model, $match)) {
            $storage = $match[1].strtoupper($match[2]);
            $model = trim(substr($model, 0, -strlen($match[0])));
        }

        return [$model, $storage];
    }

    private function storageType(string $brand): string
    {
        return $brand === 'Apple' ? 'NVMe' : 'UFS';
    }

    private function number(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/[^0-9.]/', '', (string) $value);
        return $normalized === '' ? null : (float) $normalized;
    }

    private function clean(?string $value): string
    {
        return trim((string) $value);
    }

    private function releaseDate(?string $year): ?string
    {
        $year = (int) preg_replace('/[^0-9]/', '', (string) $year);
        return $year >= 2000 && $year <= 2100 ? sprintf('%04d-01-01', $year) : null;
    }
}
