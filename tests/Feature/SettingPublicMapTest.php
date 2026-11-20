<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingPublicMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_map_returns_expected_keys(): void
    {
        $map = Setting::publicMap();

        $this->assertArrayHasKey('brand.primary_tagline', $map);
        $this->assertArrayHasKey('contact.email', $map);
        $this->assertArrayHasKey('contact.whatsapp', $map);
        $this->assertArrayHasKey('analytics.ga4_id', $map);
    }

    public function test_set_invalidates_public_map_cache(): void
    {
        Setting::publicMap();
        Setting::set('contact.email', 'updated@elkrisfoods.com');

        $this->assertSame('updated@elkrisfoods.com', Setting::publicMap()['contact.email']);
    }
}
