<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpec;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_detail_page_renders_its_date_brand_and_specifications(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        $device = Device::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Acme One',
            'slug' => 'acme-one',
            'release_date' => '2026-09-01',
        ]);

        DeviceSpec::query()->create([
            'device_id' => $device->id,
            'category' => 'Display',
            'spec_key' => 'Size',
            'spec_value' => '6.1 inches',
        ]);

        $this->get(route('devices.show', $device))
            ->assertSeeText('Acme One')
            ->assertSeeText('Acme')
            ->assertSeeText('Released Sep 2026')
            ->assertSeeText('Display')
            ->assertSeeText('6.1 inches')
            ->assertSee('rel="canonical" href="'.route('devices.show', $device).'"', false)
            ->assertSee('"@type":"Product"', false);
    }
}
