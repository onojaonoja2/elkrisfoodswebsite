<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst($this->faker->unique()->words(3, true)).' Recipe';

        return [
            'product_id' => Product::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => $this->faker->randomElement([
                'Swallow Recipes',
                'Breakfast Recipes',
                'Baking',
                'Healthy Meals',
                "Kids' Meals",
                'Quick Meals',
            ]),
            'image_path' => '/media/lifestyle/woman-with-products.png',
            'prep_minutes' => $this->faker->numberBetween(5, 45),
            'servings' => $this->faker->numberBetween(2, 6),
            'difficulty' => $this->faker->randomElement(['Easy', 'Medium']),
            'ingredients' => ['1 cup Elkris flour', '2 cups water', 'Pinch of salt'],
            'steps' => ['Boil water', 'Stir in flour', 'Mix until smooth'],
            'tips' => 'Stir continuously for a smooth, lump-free consistency.',
            'summary' => $this->faker->sentence(),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
