<?php

namespace App\Concerns;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Support\Collection;

trait BuildsStructuredData
{
    protected function productSchema(Product $product, ?float $ratingAverage = null, int $ratingCount = 0): array
    {
        $isAvailable = $product->variants
            ->contains(fn ($variant) => $variant->is_available);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'url' => route('products.show', $product),
            'description' => $product->tagline ?? $product->description ?? $product->name,
            'brand' => ['@type' => 'Brand', 'name' => config('app.name')],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product),
                'availability' => $isAvailable
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];

        if ($product->hero_image) {
            $schema['image'] = config('app.url').$product->hero_image;
        }

        if ($ratingCount > 0 && $ratingAverage !== null) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $ratingAverage,
                'reviewCount' => $ratingCount,
            ];
        }

        return $schema;
    }

    protected function recipeSchema(Recipe $recipe): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $recipe->title,
            'description' => $recipe->summary ?? $recipe->title,
            'author' => ['@type' => 'Organization', 'name' => config('app.name')],
            'prepTime' => 'PT'.(int) $recipe->prep_minutes.'M',
            'recipeYield' => ($recipe->servings ?? 4).' servings',
            'recipeCategory' => $recipe->category,
            'recipeIngredient' => $recipe->ingredients ?? [],
            'recipeInstructions' => collect($recipe->steps ?? [])->map(fn (string $step, int $index) => [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'text' => $step,
            ])->values()->all(),
        ];

        if ($recipe->created_at) {
            $schema['datePublished'] = $recipe->created_at->toIso8601String();
        }

        if ($recipe->image_path) {
            $schema['image'] = config('app.url').$recipe->image_path;
        }

        return $schema;
    }

    protected function articleSchema(Article $article): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'url' => route('health.show', $article),
            'description' => $article->meta_description ?? $article->excerptOrSummary(),
            'mainEntityOfPage' => route('health.show', $article),
            'author' => ['@type' => 'Person', 'name' => $article->author],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => ['@type' => 'ImageObject', 'url' => config('app.url').'/apple-touch-icon.png'],
            ],
        ];

        $publishedAt = $article->published_at ?? $article->created_at;

        if ($publishedAt) {
            $schema['datePublished'] = $publishedAt->toIso8601String();
        }

        if ($article->cover_path) {
            $schema['image'] = config('app.url').$article->cover_path;
        }

        return $schema;
    }

    /**
     * @param  Collection<int, Faq>  $faqs
     */
    protected function faqSchema(Collection $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn (Faq $faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
            ])->values()->all(),
        ];
    }
}
