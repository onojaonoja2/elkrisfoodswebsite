<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_xml_with_static_pages(): void
    {
        $response = $this->get(route('sitemap.index'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('<loc>'.route('home').'</loc>', $response->getContent());
        $this->assertStringContainsString('<loc>'.route('about').'</loc>', $response->getContent());
        $this->assertStringContainsString('<loc>'.route('products.index').'</loc>', $response->getContent());
    }

    public function test_sitemap_lists_published_content_and_excludes_drafts(): void
    {
        $product = Product::factory()->create(['slug' => 'oat-flour-sitemap', 'is_published' => true]);
        Product::factory()->create(['slug' => 'hidden-product-sitemap', 'is_published' => false]);

        $recipe = Recipe::factory()->create(['slug' => 'oat-swallow-sitemap', 'is_published' => true]);
        Recipe::factory()->create(['slug' => 'hidden-recipe-sitemap', 'is_published' => false]);

        Article::factory()->create(['slug' => 'fibre-guide-sitemap', 'is_published' => true]);
        Article::factory()->draft()->create(['slug' => 'hidden-article-sitemap']);

        $content = $this->get(route('sitemap.index'))->assertOk()->getContent();

        $this->assertStringContainsString(route('products.show', $product), $content);
        $this->assertStringContainsString(route('recipes.show', $recipe), $content);
        $this->assertStringContainsString(route('health.show', ['article' => 'fibre-guide-sitemap']), $content);

        $this->assertStringNotContainsString('hidden-product-sitemap', $content);
        $this->assertStringNotContainsString('hidden-recipe-sitemap', $content);
        $this->assertStringNotContainsString('hidden-article-sitemap', $content);
    }

    public function test_robots_txt_disallows_private_areas_and_links_sitemap(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Disallow: /admin', false);
        $response->assertSee('Disallow: /account', false);
        $response->assertSee('Sitemap: '.route('sitemap.index'), false);
    }
}
