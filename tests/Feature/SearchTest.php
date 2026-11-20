<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_matching_results_across_content_types(): void
    {
        $product = Product::factory()->create(['name' => 'Elkris Oat Flour', 'is_published' => true]);
        Recipe::factory()->create(['title' => 'Oat Porridge Bowl', 'is_published' => true]);
        Article::factory()->create(['title' => 'All About Oats', 'is_published' => true]);
        Store::factory()->create(['name' => 'Oats & More Supermarket', 'is_published' => true]);

        $this->get(route('search.index', ['q' => 'oat']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('search/index')
                ->where('q', 'oat')
                ->where('total', 4)
                ->where('results.0.type', 'product')
                ->where('results.0.url', route('products.show', $product)));
    }

    public function test_search_excludes_unpublished_content(): void
    {
        Product::factory()->create(['name' => 'Hidden Oat Flour', 'is_published' => false]);
        Article::factory()->draft()->create(['title' => 'Hidden Oat Article']);

        $this->get(route('search.index', ['q' => 'oat']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('search/index')
                ->where('total', 0)
                ->has('results', 0));
    }

    public function test_search_requires_at_least_two_characters(): void
    {
        Product::factory()->create(['name' => 'Elkris Oat Flour', 'is_published' => true]);

        $this->get(route('search.index', ['q' => 'o']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('search/index')
                ->where('q', 'o')
                ->where('total', 0));
    }

    public function test_search_handles_empty_query(): void
    {
        $this->get(route('search.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('search/index')
                ->where('q', '')
                ->where('total', 0));
    }
}
