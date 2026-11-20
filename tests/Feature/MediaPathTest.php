<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_media_accessors_store_paths_and_return_public_urls(): void
    {
        $product = Product::factory()->create([
            'hero_image' => '/media/products/hero.jpg',
            'og_image' => 'https://cdn.example.com/og.jpg',
            'gallery' => [
                '/media/products/one.jpg',
                'media/products/two.jpg',
                'https://cdn.example.com/three.jpg',
            ],
        ]);

        $product->refresh();

        $this->assertSame('media/products/hero.jpg', $product->getRawOriginal('hero_image'));
        $this->assertSame('/media/products/hero.jpg', $product->hero_image);
        $this->assertSame('https://cdn.example.com/og.jpg', $product->og_image);
        $this->assertSame([
            '/media/products/one.jpg',
            '/media/products/two.jpg',
            'https://cdn.example.com/three.jpg',
        ], $product->gallery);
    }

    public function test_product_gallery_defaults_to_an_empty_array(): void
    {
        $product = Product::factory()->create(['gallery' => null]);

        $this->assertSame([], $product->gallery);
    }
}
