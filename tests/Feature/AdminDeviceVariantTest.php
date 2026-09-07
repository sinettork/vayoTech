<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDeviceVariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_device_update_persists_device_variants(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'variants@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
        $brand = Brand::query()->create(['name' => 'Acme', 'slug' => 'acme']);
        $device = Device::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Acme One',
            'slug' => 'acme-one',
            'status' => 'available',
        ]);

        $this->actingAs($user)
            ->put(route('admin.devices.update', $device), [
                'brand_id' => $brand->id,
                'name' => $device->name,
                'slug' => $device->slug,
                'status' => $device->status,
                'variants' => [
                    [
                        'ram' => '8GB',
                        'storage' => '128GB',
                        'storage_type' => 'UFS 4.0',
                        'model_code' => 'AC-ONE-128',
                        'market' => 'Global',
                        'price' => '799',
                        'currency' => 'USD',
                        'is_default' => '1',
                    ],
                    [
                        'ram' => '12GB',
                        'storage' => '256GB',
                        'currency' => 'USD',
                        'is_default' => '0',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.devices.index'));

        $this->assertDatabaseHas('device_variants', [
            'device_id' => $device->id,
            'ram' => '8GB',
            'storage' => '128GB',
            'model_code' => 'AC-ONE-128',
            'is_default' => true,
            'sort_order' => 0,
        ]);
        $this->assertSame(2, DeviceVariant::query()->where('device_id', $device->id)->count());
    }
}
