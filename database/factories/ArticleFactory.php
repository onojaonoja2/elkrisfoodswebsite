<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst($this->faker->unique()->words(4, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => $this->faker->randomElement(Article::CATEGORIES),
            'excerpt' => $this->faker->sentence(),
            'body' => collect($this->faker->paragraphs(3))
                ->map(fn (string $paragraph): string => "<p>{$paragraph}</p>")
                ->implode(''),
            'cover_path' => '/media/lifestyle/woman-with-products.png',
            'author' => $this->faker->name(),
            'reading_minutes' => $this->faker->numberBetween(2, 8),
            'meta_description' => $this->faker->sentence(14),
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-6 months'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
