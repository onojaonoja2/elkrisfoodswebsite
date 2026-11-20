<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_signup_stores_subscriber_and_flashes_success(): void
    {
        $response = $this->post(route('newsletter.store'), [
            'email' => 'Ada@Example.com',
            'source' => 'footer',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscribers', [
            'email' => 'ada@example.com',
            'source' => 'footer',
        ]);
        $this->assertNotNull(Subscriber::first()->subscribed_at);
    }

    public function test_newsletter_signup_validates_email(): void
    {
        $response = $this->post(route('newsletter.store'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(0, Subscriber::query()->count());
    }

    public function test_newsletter_signup_is_idempotent_for_the_same_email(): void
    {
        $this->post(route('newsletter.store'), ['email' => 'ada@example.com']);
        $this->post(route('newsletter.store'), ['email' => 'ADA@example.com']);

        $this->assertSame(1, Subscriber::query()->count());
    }

    public function test_newsletter_signup_is_throttled(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('newsletter.store'), ['email' => "subscriber{$i}@example.com"]);
        }

        $this->post(route('newsletter.store'), ['email' => 'sixth@example.com'])
            ->assertStatus(429);
    }
}
