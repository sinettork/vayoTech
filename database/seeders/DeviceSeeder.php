<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpec;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::firstOrCreate([
            'name' => 'Apple',
        ], [
            'slug' => 'apple',
        ]);

        $device = Device::updateOrCreate([
            'slug' => 'iphone-16-pro',
        ], [
            'brand_id' => $brand->id,
            'name' => 'iPhone 16 Pro',
            'release_date' => '2024-09-20',
            'status' => 'available',
        ]);

        $specs = [
            ['Display', 'Screen Size', '6.3 inches'],
            ['Display', 'Resolution', '2622 x 1206'],
            ['Camera', 'Main Camera', '48 MP'],
            ['Battery', 'Capacity', '3582 mAh'],
        ];

        foreach ($specs as $i => [$category, $key, $value]) {
            DeviceSpec::updateOrCreate([
                'device_id' => $device->id,
                'category' => $category,
                'spec_key' => $key,
            ], [
                'spec_value' => $value,
                'sort_order' => $i,
            ]);
        }
    }
}
