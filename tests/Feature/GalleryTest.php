<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_index_renders_published_items_and_video_count(): void
    {
        GalleryItem::factory()->create(['title' => 'Visible Photo', 'media_type' => GalleryItem::MEDIA_IMAGE, 'sort_order' => 0, 'is_published' => true]);
        GalleryItem::factory()->video('/media/videos/oat-flour.mp4')->create(['title' => 'Visible Video', 'sort_order' => 1, 'is_published' => true]);
        GalleryItem::factory()->create(['title' => 'Hidden Photo', 'media_type' => GalleryItem::MEDIA_IMAGE, 'is_published' => false]);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('gallery/index')
                ->has('items', 2)
                ->where('videoCount', 1)
                ->where('items.0.title', 'Visible Photo'));
    }

    public function test_gallery_index_filters_by_category(): void
    {
        GalleryItem::factory()->create(['category' => 'Products', 'is_published' => true]);
        GalleryItem::factory()->create(['category' => 'Customers', 'is_published' => true]);

        $this->get(route('gallery.index', ['category' => 'Products']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('gallery/index')
                ->has('items', 1)
                ->where('activeCategory', 'Products'));
    }

    public function test_homepage_includes_videos_from_gallery(): void
    {
        GalleryItem::factory()->video('/media/videos/preparation.mp4')
            ->create(['title' => 'How to prepare', 'sort_order' => 1, 'is_published' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('home')
                ->has('videos', 1)
                ->where('videos.0.title', 'How to prepare')
                ->where('videos.0.media_url', '/media/videos/preparation.mp4')
                ->where('heroVideo', '/media/videos/preparation.mp4'));
    }

    public function test_homepage_falls_back_to_default_hero_video_without_gallery_videos(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('home')
                ->where('heroVideo', '/media/videos/brand-positioning.mp4'));
    }

    public function test_gallery_item_media_url_resolves_s3_paths(): void
    {
        Storage::fake('s3');

        $item = GalleryItem::factory()->video('gallery/website/videos/oat-flour.mp4')->create();

        $this->assertStringContainsString(
            'gallery/website/videos/oat-flour.mp4',
            $item->media_url,
        );
    }
}
