<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoSchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonLdBlocks(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        return array_map(
            fn (string $block) => json_decode($block, true),
            $matches[1],
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array<string, mixed>|null
     */
    private function blockOfType(array $blocks, string $type): ?array
    {
        foreach ($blocks as $block) {
            if (($block['@type'] ?? null) === $type) {
                return $block;
            }
        }

        return null;
    }

    public function test_pages_include_organization_and_website_schema(): void
    {
        $blocks = $this->jsonLdBlocks($this->get(route('home'))->assertOk()->getContent());

        $organization = $this->blockOfType($blocks, 'Organization');
        $website = $this->blockOfType($blocks, 'WebSite');

        $this->assertNotNull($organization);
        $this->assertSame(config('app.name'), $organization['name']);
        $this->assertNotNull($website);
        $this->assertStringContainsString('/search?q=', $website['potentialAction']['target']['urlTemplate']);
    }

    public function test_homepage_includes_faq_schema_from_published_faqs(): void
    {
        $faq = Faq::factory()->create([
            'question' => 'Is Elkris flour gluten free?',
            'answer' => 'Yes, our flours are made from naturally gluten-free grains.',
            'is_published' => true,
        ]);
        Faq::factory()->create(['is_published' => false]);

        $blocks = $this->jsonLdBlocks($this->get(route('home'))->assertOk()->getContent());

        $faqPage = $this->blockOfType($blocks, 'FAQPage');
        $this->assertNotNull($faqPage);
        $this->assertCount(1, $faqPage['mainEntity']);
        $this->assertSame($faq->question, $faqPage['mainEntity'][0]['name']);
        $this->assertSame(
            'Yes, our flours are made from naturally gluten-free grains.',
            $faqPage['mainEntity'][0]['acceptedAnswer']['text'],
        );
    }

    public function test_product_page_includes_product_and_offer_schema_without_price(): void
    {
        $product = Product::factory()->create([
            'name' => 'Elkris Oat Flour',
            'is_published' => true,
        ]);
        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'is_available' => true,
        ]);

        $blocks = $this->jsonLdBlocks(
            $this->get(route('products.show', $product))->assertOk()->getContent(),
        );

        $schema = $this->blockOfType($blocks, 'Product');
        $this->assertNotNull($schema);
        $this->assertSame('Elkris Oat Flour', $schema['name']);
        $this->assertSame(route('products.show', $product), $schema['url']);
        $this->assertArrayNotHasKey('price', $schema['offers']);
        $this->assertArrayNotHasKey('priceCurrency', $schema['offers']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
    }

    public function test_recipe_page_includes_recipe_schema(): void
    {
        $recipe = Recipe::factory()->create([
            'title' => 'Oat Swallow',
            'is_published' => true,
            'prep_minutes' => 10,
            'servings' => 4,
            'steps' => ['Boil water', 'Stir in flour'],
        ]);

        $blocks = $this->jsonLdBlocks(
            $this->get(route('recipes.show', $recipe))->assertOk()->getContent(),
        );

        $schema = $this->blockOfType($blocks, 'Recipe');
        $this->assertNotNull($schema);
        $this->assertSame('Oat Swallow', $schema['name']);
        $this->assertSame('PT10M', $schema['prepTime']);
        $this->assertSame('4 servings', $schema['recipeYield']);
        $this->assertCount(2, $schema['recipeInstructions']);
        $this->assertSame('Boil water', $schema['recipeInstructions'][0]['text']);
    }

    public function test_health_article_includes_article_schema(): void
    {
        $article = Article::factory()->create([
            'title' => 'Fibre Rich Meals',
            'author' => 'Elkris Nutrition Team',
            'is_published' => true,
        ]);

        $blocks = $this->jsonLdBlocks(
            $this->get(route('health.show', $article))->assertOk()->getContent(),
        );

        $schema = $this->blockOfType($blocks, 'Article');
        $this->assertNotNull($schema);
        $this->assertSame('Fibre Rich Meals', $schema['headline']);
        $this->assertSame('Elkris Nutrition Team', $schema['author']['name']);
        $this->assertSame(route('health.show', $article), $schema['mainEntityOfPage']);
    }

    public function test_schema_blocks_are_valid_json(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();
        $blocks = $this->jsonLdBlocks($html);

        $this->assertNotEmpty($blocks);

        foreach ($blocks as $block) {
            $this->assertIsArray($block);
            $this->assertArrayHasKey('@type', $block);
        }
    }
}
