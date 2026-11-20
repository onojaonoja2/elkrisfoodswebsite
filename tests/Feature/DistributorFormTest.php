<?php

namespace Tests\Feature;

use App\Models\DistributorLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistributorFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_distributor_form_validates_required_fields(): void
    {
        $response = $this->post(route('distributor.store'), []);

        $response->assertSessionHasErrors(['name', 'phone', 'email', 'state']);
        $this->assertSame(0, DistributorLead::count());
    }

    public function test_distributor_form_stores_an_application(): void
    {
        $payload = [
            'name' => 'John Distributor',
            'company' => 'John Stores Ltd',
            'phone' => '08098765432',
            'email' => 'john@example.com',
            'state' => 'Lagos',
            'lga' => 'Ikeja',
            'business_type' => 'Retailer',
            'monthly_order' => '1-5 cartons',
            'locations' => 'Ikeja, Surulere',
        ];

        $response = $this->from(route('distributor.create'))
            ->post(route('distributor.store'), $payload);

        $response->assertRedirect(route('distributor.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('distributor_leads', [
            'name' => 'John Distributor',
            'state' => 'Lagos',
            'email' => 'john@example.com',
        ]);
    }
}
