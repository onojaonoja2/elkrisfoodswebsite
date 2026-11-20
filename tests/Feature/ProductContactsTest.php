<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductContactsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_exposes_the_product_whatsapp_number(): void
    {
        $product = Product::factory()->create([
            'whatsapp' => '07074868813',
            'is_published' => true,
        ]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/show')
                ->where('product.whatsapp', '07074868813'));
    }

    public function test_product_page_exposes_null_whatsapp_when_no_rep_is_assigned(): void
    {
        $product = Product::factory()->create([
            'whatsapp' => null,
            'is_published' => true,
        ]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('product.whatsapp', null));
    }

    public function test_shop_page_exposes_the_site_whatsapp_number(): void
    {
        Setting::set('contact.whatsapp', '07074711387');
        Product::factory()->create(['is_published' => true]);

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('shop/index')
                ->where(
                    'settings',
                    fn ($settings) => $settings['contact.whatsapp'] === '07074711387',
                ));
    }

    public function test_recipe_page_exposes_the_product_whatsapp_number(): void
    {
        $product = Product::factory()->create([
            'whatsapp' => '07074892727',
            'is_published' => true,
        ]);
        $recipe = Recipe::factory()->for($product)->create(['is_published' => true]);

        $this->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('recipes/show')
                ->where('recipe.product.whatsapp', '07074892727'));
    }

    public function test_plain_flour_url_redirects_to_premium_flours(): void
    {
        $this->get('/products/elkris-plain-flour')
            ->assertRedirect('/products/elkris-premium-flours');
    }
}
