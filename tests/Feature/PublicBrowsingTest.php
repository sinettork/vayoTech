<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpec;
use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_index_filters_by_brand_and_status(): void
    {
        $apple = Brand::query()->create([
            'name' => 'Apple',
            'slug' => 'apple',
            'description' => 'Apple phone specifications and release information.',
        ]);
        $samsung = Brand::query()->create(['name' => 'Samsung', 'slug' => 'samsung']);

        Device::query()->create([
            'brand_id' => $apple->id,
            'name' => 'iPhone Available',
            'slug' => 'iphone-available',
            'status' => 'available',
        ]);
        Device::query()->create([
            'brand_id' => $samsung->id,
            'name' => 'Galaxy Rumored',
            'slug' => 'galaxy-rumored',
            'status' => 'rumored',
        ]);

        $this->get(route('devices.index', ['brand' => 'apple', 'status' => 'available']))
            ->assertSeeText('iPhone Available')
            ->assertSeeText('Apple Phones')
            ->assertSeeText('Apple phone specifications and release information.')
            ->assertDontSeeText('Galaxy Rumored');
    }

    public function test_search_requires_two_characters_and_returns_matching_devices(): void
    {
        $brand = Brand::query()->create(['name' => 'Google', 'slug' => 'google']);
        Device::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Pixel Test',
            'slug' => 'pixel-test',
            'status' => 'available',
        ]);

        $this->getJson(route('devices.search', ['q' => 'p']))
            ->assertExactJson([]);

        $this->getJson(route('devices.search', ['q' => 'pixel']))
            ->assertJsonPath('0.name', 'Pixel Test')
            ->assertJsonPath('0.brand', 'Google');
    }

    public function test_comparison_uses_only_the_first_four_valid_device_ids(): void
    {
        $brand = Brand::query()->create(['name' => 'OnePlus', 'slug' => 'oneplus']);
        $devices = collect(range(1, 5))->map(function (int $number) use ($brand): Device {
            $device = Device::query()->create([
                'brand_id' => $brand->id,
                'name' => "OnePlus {$number}",
                'slug' => "oneplus-{$number}",
                'status' => 'available',
            ]);

            DeviceSpec::query()->create([
                'device_id' => $device->id,
                'category' => 'Display',
                'spec_key' => 'Size',
                'spec_value' => "{$number}.0 inches",
            ]);

            return $device;
        });

        $this->get(route('compare.index', ['devices' => $devices->pluck('id')->implode(',').',invalid']))
            ->assertSeeText('OnePlus 1')
            ->assertSeeText('OnePlus 4')
            ->assertDontSeeText('5.0 inches');
    }

    public function test_news_body_is_escaped_and_security_headers_are_returned(): void
    {
        $post = NewsPost::query()->create([
            'title' => 'Safety update',
            'slug' => 'safety-update',
            'body' => '<script>alert("unsafe")</script>',
            'published_at' => now(),
        ]);

        $this->get(route('news.show', $post))
            ->assertSeeText('<script>alert("unsafe")</script>')
            ->assertDontSee('<script>alert("unsafe")</script>', false)
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
