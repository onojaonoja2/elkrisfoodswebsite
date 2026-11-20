<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentContentResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_articles_resource_pages_render(): void
    {
        $article = Article::factory()->create();

        $this->actingAs($this->admin())->get('/admin/articles')->assertOk();
        $this->actingAs($this->admin())->get('/admin/articles/create')->assertOk();
        $this->actingAs($this->admin())->get('/admin/articles/'.$article->id.'/edit')->assertOk();
    }

    public function test_gallery_items_resource_pages_render(): void
    {
        $item = GalleryItem::factory()->create();

        $this->actingAs($this->admin())->get('/admin/gallery-items')->assertOk();
        $this->actingAs($this->admin())->get('/admin/gallery-items/create')->assertOk();
        $this->actingAs($this->admin())->get('/admin/gallery-items/'.$item->id.'/edit')->assertOk();
    }

    public function test_subscribers_resource_pages_render(): void
    {
        $subscriber = Subscriber::factory()->create();

        $this->actingAs($this->admin())->get('/admin/subscribers')->assertOk();
        $this->actingAs($this->admin())->get('/admin/subscribers/'.$subscriber->id.'/edit')->assertOk();
    }

    public function test_subscribers_resource_requires_authentication(): void
    {
        $this->get('/admin/subscribers')->assertRedirect();
    }
}
