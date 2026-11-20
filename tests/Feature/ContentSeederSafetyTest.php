<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseeding_preserves_existing_content_and_does_not_duplicate_records(): void
    {
        $this->seed(ContentSeeder::class);

        $product = Product::query()->where('slug', 'elkris-oat-flour')->firstOrFail();
        $product->update(['name' => 'Custom Oat Flour']);
        Setting::set('brand.primary_tagline', 'Custom Tagline');

        $productCount = Product::query()->count();

        $this->seed(ContentSeeder::class);

        $this->assertSame('Custom Oat Flour', $product->fresh()->name);
        $this->assertSame('Custom Tagline', Setting::get('brand.primary_tagline'));
        $this->assertSame($productCount, Product::query()->count());
    }

    public function test_seeding_assigns_social_links_and_product_whatsapp_numbers(): void
    {
        foreach (['contact.whatsapp', 'social.facebook', 'social.instagram', 'social.tiktok', 'social.youtube'] as $key) {
            Setting::set($key, '');
        }

        $this->seed(ContentSeeder::class);

        $this->assertSame('07074711387', Setting::get('contact.whatsapp'));
        $this->assertSame('https://www.facebook.com/Elkrisfoods', Setting::get('social.facebook'));
        $this->assertSame('https://www.instagram.com/elkrisfoods1', Setting::get('social.instagram'));
        $this->assertSame('https://www.tiktok.com/@elkris.foods', Setting::get('social.tiktok'));
        $this->assertSame('https://www.youtube.com/@elkrisfoods', Setting::get('social.youtube'));

        $premiumFlours = Product::query()->where('slug', 'elkris-premium-flours')->firstOrFail();

        $this->assertSame('Elkris Premium Flours', $premiumFlours->name);
        $this->assertSame('07074711387', $premiumFlours->whatsapp);
        $this->assertSame('07074868813', Product::query()->where('slug', 'elkris-oat-flour')->value('whatsapp'));
        $this->assertSame('07074892727', Product::query()->where('slug', 'elkris-oat-and-fibre-flour')->value('whatsapp'));
        $this->assertSame('09139584190', Product::query()->where('slug', 'elkris-instant-plantain-flour')->value('whatsapp'));
    }
}
