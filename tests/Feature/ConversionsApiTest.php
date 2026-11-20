<?php

namespace Tests\Feature;

use App\Jobs\SendMetaConversionEvent;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConversionsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_posts_to_meta_graph_api_when_configured(): void
    {
        Setting::set('analytics.meta_pixel_id', '111222333');
        Setting::set('analytics.meta_capi_token', 'token-abc');

        Http::fake();

        (new SendMetaConversionEvent(
            'Lead',
            ['content_name' => 'Newsletter Signup'],
            ['email' => 'Ada@Example.com', 'ip' => '1.2.3.4', 'user_agent' => 'PHPUnit'],
            'http://localhost/newsletter',
        ))->handle();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'graph.facebook.com/v21.0/111222333/events')
                && $request->method() === 'POST'
                && $request['access_token'] === 'token-abc'
                && $request['data'][0]['event_name'] === 'Lead'
                && $request['data'][0]['action_source'] === 'website'
                && $request['data'][0]['custom_data']['content_name'] === 'Newsletter Signup'
                && $request['data'][0]['user_data']['em'] === [hash('sha256', 'ada@example.com')]
                && $request['data'][0]['user_data']['client_ip_address'] === '1.2.3.4';
        });
    }

    public function test_event_is_skipped_without_access_token(): void
    {
        Setting::set('analytics.meta_pixel_id', '111222333');

        Http::fake();

        (new SendMetaConversionEvent('Lead', ['content_name' => 'Contact Form']))->handle();

        Http::assertNothingSent();
    }

    public function test_event_is_skipped_without_pixel_id(): void
    {
        Setting::set('analytics.meta_capi_token', 'token-abc');

        Http::fake();

        (new SendMetaConversionEvent('Lead'))->handle();

        Http::assertNothingSent();
    }

    public function test_test_event_code_is_included_when_configured(): void
    {
        Setting::set('analytics.meta_pixel_id', '111222333');
        Setting::set('analytics.meta_capi_token', 'token-abc');
        Setting::set('analytics.meta_capi_test_event_code', 'TEST-EVENT-42');

        Http::fake();

        (new SendMetaConversionEvent('Lead'))->handle();

        Http::assertSent(fn ($request) => ($request['test_event_code'] ?? null) === 'TEST-EVENT-42');
    }

    public function test_newsletter_signup_dispatches_conversion_event(): void
    {
        Bus::fake([SendMetaConversionEvent::class]);

        $this->post(route('newsletter.store'), ['email' => 'lead@example.com'])
            ->assertSessionHasNoErrors();

        Bus::assertDispatched(
            SendMetaConversionEvent::class,
            fn (SendMetaConversionEvent $job): bool => $job->eventName === 'Lead'
                && $job->customData['content_name'] === 'Newsletter Signup',
        );
    }

    public function test_contact_form_dispatches_conversion_event(): void
    {
        Bus::fake([SendMetaConversionEvent::class]);

        $this->post(route('contact.store'), [
            'intent' => 'order',
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'message' => 'Hello from the contact page.',
        ])->assertSessionHasNoErrors();

        Bus::assertDispatched(
            SendMetaConversionEvent::class,
            fn (SendMetaConversionEvent $job): bool => $job->customData['content_name'] === 'Contact Form',
        );
    }
}
