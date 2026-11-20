<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_homepage_defaults_to_light_appearance(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('class="dark"', false);
    }

    public function test_dark_appearance_cookie_renders_dark_html(): void
    {
        $this->withUnencryptedCookie('appearance', 'dark')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('class="dark"', false);
    }

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))->assertOk();
    }

    public function test_products_index_renders_with_products(): void
    {
        Product::factory()->create(['is_published' => true]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('products/index'));
    }

    public function test_product_show_renders_for_published_product(): void
    {
        $product = Product::factory()->create(['is_published' => true]);

        $this->get(route('products.show', $product))->assertOk();
    }

    public function test_shop_index_renders(): void
    {
        Product::factory()->create(['is_published' => true]);

        $this->get(route('shop.index'))->assertOk();
    }

    public function test_public_product_pages_do_not_display_prices(): void
    {
        $product = Product::factory()->create(['is_published' => true]);

        $routes = [
            route('home'),
            route('products.index'),
            route('shop.index'),
            route('products.show', $product),
        ];

        foreach ($routes as $url) {
            $content = $this->get($url)->assertOk()->getContent();

            $this->assertStringNotContainsString('₦', $content);
            $this->assertStringNotContainsString('priceCurrency', $content);
            $this->assertStringNotContainsString('Add to cart', $content);
        }
    }

    public function test_recipes_index_renders(): void
    {
        Recipe::factory()->create(['is_published' => true]);

        $this->get(route('recipes.index'))->assertOk();
    }

    public function test_recipe_show_renders(): void
    {
        $recipe = Recipe::factory()->create(['is_published' => true]);

        $this->get(route('recipes.show', $recipe))->assertOk();
    }

    public function test_store_locator_renders(): void
    {
        Store::factory()->create(['is_published' => true]);

        $this->get(route('stores.index'))->assertOk();
    }

    public function test_contact_page_renders(): void
    {
        $this->get(route('contact.index'))->assertOk();
    }

    public function test_distributor_page_renders(): void
    {
        $this->get(route('distributor.create'))->assertOk();
    }

    public function test_pages_do_not_contain_template_placeholder_content(): void
    {
        $forbidden = ['help@company.com', 'New York, NY'];

        $routes = [
            route('home'),
            route('about'),
            route('contact.index'),
            route('distributor.create'),
            route('products.index'),
            route('shop.index'),
            route('recipes.index'),
            route('stores.index'),
            route('health.index'),
            route('gallery.index'),
            route('search.index', ['q' => 'oat']),
        ];

        foreach ($routes as $url) {
            $content = $this->get($url)->assertOk()->getContent();

            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString(
                    $needle,
                    $content,
                    "Route [{$url}] contains forbidden template content [{$needle}].",
                );
            }
        }
    }

    public function test_admin_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_admin_dashboard_requires_an_admin_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }
}
