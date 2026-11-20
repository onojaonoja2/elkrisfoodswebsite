<?php

namespace Tests\Feature;

use App\Models\DistributorLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DistributorStatusTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'name' => 'Tunde Adeyemi',
            'company' => 'Tunde Stores',
            'phone' => '08012345678',
            'email' => 'tunde@example.com',
            'state' => 'Lagos',
        ];
    }

    public function test_application_generates_reference_and_shows_it_in_flash(): void
    {
        $response = $this->post(route('distributor.store'), $this->validPayload());

        $lead = DistributorLead::query()->firstOrFail();

        $this->assertMatchesRegularExpression('/^DST-[A-Z0-9]{6}$/', $lead->reference);
        $response->assertSessionHas('success', fn (string $message): bool => str_contains($message, $lead->reference));
    }

    public function test_status_page_resolves_valid_reference(): void
    {
        $lead = DistributorLead::factory()->create(['status' => 'in_progress']);

        $this->get(route('distributor.status', ['ref' => $lead->reference]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('distributor-status')
                ->where('lead.reference', $lead->reference)
                ->where('lead.status', 'in_progress'));
    }

    public function test_status_page_renders_without_reference(): void
    {
        $this->get(route('distributor.status'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('distributor-status')
                ->where('lead', null));
    }

    public function test_status_page_returns_null_for_unknown_reference(): void
    {
        $this->get(route('distributor.status', ['ref' => 'DST-XXXXXX']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('lead', null));
    }
}
