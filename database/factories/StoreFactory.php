<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $state = $this->faker->randomElement(['Lagos', 'Abuja', 'Rivers', 'Ogun', 'Enugu']);

        return [
            'name' => $this->faker->company().' Store',
            'address' => $this->faker->streetAddress(),
            'city' => $state === 'Lagos' ? $this->faker->randomElement(['Lekki', 'Ikeja', 'Yaba', 'Surulere']) : $state,
            'state' => $state,
            'phone' => $this->faker->phoneNumber(),
            'opening_hours' => 'Mon - Sat: 8am - 8pm',
            'latitude' => $this->faker->latitude(4, 14),
            'longitude' => $this->faker->longitude(2, 15),
            'is_published' => true,
        ];
    }
}
