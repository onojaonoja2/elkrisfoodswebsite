<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Elkris '.$this->faker->unique()->words(2, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'tagline' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'benefits' => [
                'Helps support a balanced diet',
                'High in dietary fibre',
                'Versatile for everyday meals',
            ],
            'best_with' => ['Egusi', 'Okro', 'Ogbono'],
            'nutrition_notes' => $this->faker->sentence(),
            'hero_image' => '/media/products/product-range.png',
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_published' => true,
            'is_featured' => false,
        ];
    }
}
