<?php

namespace Tests\Feature;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_its_seo_metadata(): void
    {
        $this->get(route('home'))
            ->assertSee('name="description" content="Compare smartphone specifications, browse the latest devices, and read mobile technology news."', false)
            ->assertSee('rel="canonical" href="'.route('home').'"', false)
            ->assertSee('property="og:site_name" content="PhoneSpecs"', false)
            ->assertSeeText('Latest Devices');
    }

    public function test_legal_pages_are_available(): void
    {
        $this->get(route('privacy'))
            ->assertSeeText('Privacy Policy');

        $this->get(route('terms'))
            ->assertSeeText('Terms of Use');
    }

    public function test_homepage_cache_is_invalidated_when_a_brand_is_created(): void
    {
        Cache::forget('homepage.data.v3');

        $this->get(route('home'));

        $this->assertSame([
            'latestDeviceIds' => [],
            'latestNewsIds' => [],
            'brandIds' => [],
        ], Cache::get('homepage.data.v3'));

        Brand::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
        ]);

        $this->get(route('home'))
            ->assertSee('href="'.route('devices.index').'?brand=acme"', false)
            ->assertSeeText('Acme');
    }
}
