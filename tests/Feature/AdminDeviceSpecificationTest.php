<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpec;
use App\Models\SpecDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class AdminDeviceSpecificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_device_update_persists_canonical_spec_definition(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        $device = Device::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Acme One',
            'slug' => 'acme-one',
            'status' => 'available',
        ]);

        $definition = SpecDefinition::query()->where('key', 'display_size')->firstOrFail();

        DeviceSpec::query()->create([
            'device_id' => $device->id,
            'spec_definition_id' => $definition->id,
            'category' => $definition->category,
            'spec_key' => $definition->label,
            'spec_value' => '6.1 inches',
            'numeric_value' => 6.1,
            'sort_order' => 1,
        ]);

        $this->actingAs($user)
            ->put(route('admin.devices.update', $device), [
                'brand_id' => $brand->id,
                'name' => 'Acme One',
                'slug' => 'acme-one',
                'status' => 'available',
                'specs' => [
                    [
                        'definition_id' => $definition->id,
                        'spec_value' => '6.3 inches',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.devices.index'));

        $this->assertDatabaseHas('device_specs', [
            'device_id' => $device->id,
            'spec_definition_id' => $definition->id,
            'category' => 'Display',
            'spec_key' => 'Display size',
            'spec_value' => '6.3 inches',
        ]);

        $this->assertSame('6.300', DeviceSpec::query()->where('device_id', $device->id)->value('numeric_value'));
    }

    public function test_admin_device_update_rejects_duplicate_canonical_definitions(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
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
        $definition = SpecDefinition::query()->where('key', 'memory_ram')->firstOrFail();

        $this->actingAs($user)
            ->from(route('admin.devices.edit', $device))
            ->put(route('admin.devices.update', $device), [
                'brand_id' => $brand->id,
                'name' => $device->name,
                'slug' => $device->slug,
                'status' => $device->status,
                'specs' => [
                    ['definition_id' => $definition->id, 'spec_value' => '8 GB'],
                    ['definition_id' => $definition->id, 'spec_value' => '12 GB'],
                ],
            ])
            ->assertSessionHasErrors('specs');
    }
}
