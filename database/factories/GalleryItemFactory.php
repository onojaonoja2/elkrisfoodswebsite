<?php

namespace Database\Factories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isVideo = $this->faker->boolean(30);

        return [
            'title' => ucfirst($this->faker->words(3, true)),
            'caption' => $this->faker->optional()->sentence(),
            'category' => $this->faker->randomElement(GalleryItem::CATEGORIES),
            'media_type' => $isVideo ? GalleryItem::MEDIA_VIDEO : GalleryItem::MEDIA_IMAGE,
            'media_path' => $isVideo
                ? '/media/videos/'.$this->faker->randomElement(['oat-flour', 'preparation', 'brand-positioning']).'.mp4'
                : $this->faker->randomElement([
                    '/media/lifestyle/woman-with-products.png',
                    '/media/products/pack.png',
                ]),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_published' => true,
        ];
    }

    public function video(string $path): static
    {
        return $this->state(fn () => [
            'media_type' => GalleryItem::MEDIA_VIDEO,
            'media_path' => $path,
        ]);
    }
}
