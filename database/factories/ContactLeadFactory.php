<?php

namespace Database\Factories;

use App\Models\ContactLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactLead>
 */
class ContactLeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'intent' => $this->faker->randomElement([
                'order',
                'retailer',
                'product_info',
                'complaint',
                'distributor',
                'partner',
            ]),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->paragraph(),
            'status' => 'new',
        ];
    }
}
