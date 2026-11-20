<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'rating' => $this->faker->numberBetween(1, 5),
            'body' => $this->faker->paragraph(),
            'status' => Review::STATUS_APPROVED,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => Review::STATUS_PENDING]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => Review::STATUS_REJECTED]);
    }

    public function guest(): static
    {
        return $this->state(fn () => ['user_id' => null]);
    }
}
