<?php

namespace Database\Factories;

use App\Models\DistributorLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DistributorLead>
 */
class DistributorLeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'state' => $this->faker->randomElement(['Lagos', 'Abuja', 'Rivers', 'Ogun', 'Enugu']),
            'lga' => $this->faker->city(),
            'business_type' => $this->faker->randomElement(['Retailer', 'Distributor', 'Wholesaler']),
            'monthly_order' => $this->faker->randomElement(['1-5 cartons', '6-20 cartons', '20+ cartons']),
            'locations' => $this->faker->sentence(),
            'status' => 'new',
        ];
    }
}
