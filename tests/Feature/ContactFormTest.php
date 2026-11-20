<?php

namespace Tests\Feature;

use App\Models\ContactLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact.store'), []);

        $response->assertSessionHasErrors(['intent', 'name', 'email', 'message']);
        $this->assertSame(0, ContactLead::count());
    }

    public function test_contact_form_rejects_invalid_intent(): void
    {
        $response = $this->post(route('contact.store'), [
            'intent' => 'not-a-real-intent',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello there',
        ]);

        $response->assertSessionHasErrors('intent');
        $this->assertSame(0, ContactLead::count());
    }

    public function test_contact_form_stores_a_lead(): void
    {
        $payload = [
            'intent' => 'order',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '08012345678',
            'message' => 'I would like to order oat flour.',
        ];

        $response = $this->from(route('contact.index'))
            ->post(route('contact.store'), $payload);

        $response->assertRedirect(route('contact.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_leads', [
            'intent' => 'order',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'I would like to order oat flour.',
        ]);
    }
}
