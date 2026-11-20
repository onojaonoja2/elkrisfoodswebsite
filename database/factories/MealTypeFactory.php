<?php

namespace Database\Factories;

use App\Models\MealType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MealType>
 */
class MealTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_published' => true,
        ];
    }
}
