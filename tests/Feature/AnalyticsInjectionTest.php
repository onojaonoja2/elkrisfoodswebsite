<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsInjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_tags_render_when_configured(): void
    {
        Setting::set('analytics.ga4_id', 'G-TEST12345');
        Setting::set('analytics.meta_pixel_id', '1234567890');
        Setting::set('analytics.google_ads_id', 'AW-99999999');
        Setting::set('analytics.search_console_verification', 'verification-token-xyz');

        $content = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id=G-TEST12345', $content);
        $this->assertStringContainsString("gtag('config', \"G-TEST12345\"", $content);
        $this->assertStringContainsString("gtag('config', \"AW-99999999\"", $content);
        $this->assertStringContainsString('fbq(\'init\', "1234567890")', $content);
        $this->assertStringContainsString('<meta name="google-site-verification" content="verification-token-xyz">', $content);
        $this->assertStringContainsString("gtag('event', 'page_view'", $content);
    }

    public function test_analytics_tags_are_absent_when_not_configured(): void
    {
        $content = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('googletagmanager.com', $content);
        $this->assertStringNotContainsString('connect.facebook.net', $content);
        $this->assertStringNotContainsString('google-site-verification', $content);
    }

    public function test_conversions_api_token_never_reaches_the_client(): void
    {
        Setting::set('analytics.ga4_id', 'G-TEST12345');
        Setting::set('analytics.meta_pixel_id', '1234567890');
        Setting::set('analytics.meta_capi_token', 'super-secret-capi-token');
        Setting::set('analytics.meta_capi_test_event_code', 'TEST-CODE-1');

        $content = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('super-secret-capi-token', $content);
        $this->assertStringNotContainsString('TEST-CODE-1', $content);
        $this->assertStringNotContainsString('meta_capi_token', $content);

        preg_match('/data-page="([^"]+)"/', $content, $matches);
        $props = json_decode(html_entity_decode($matches[1] ?? ''), true)['props'] ?? [];

        $this->assertArrayHasKey('settings', $props);

        foreach (array_keys($props['settings']) as $key) {
            $this->assertStringNotContainsString('capi', $key);
        }
    }
}
