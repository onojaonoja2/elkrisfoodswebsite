<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->optional()->name(),
            'source' => $this->faker->randomElement(['footer', 'home']),
            'subscribed_at' => $this->faker->dateTimeBetween('-3 months'),
            'unsubscribed_at' => null,
        ];
    }
}
