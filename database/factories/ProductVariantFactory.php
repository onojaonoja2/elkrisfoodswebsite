<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
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
            'size_label' => $this->faker->randomElement(['650g', '1.3kg', '1.8kg', '2.5kg', '5kg']),
            'pack_type' => 'single',
            'price' => $this->faker->numberBetween(1500, 45000),
            'sku' => strtoupper($this->faker->bothify('ELK-####')),
            'is_available' => true,
        ];
    }
}
