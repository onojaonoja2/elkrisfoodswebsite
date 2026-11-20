<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HealthHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_index_renders_with_published_articles_only(): void
    {
        Article::factory()->create(['title' => 'Published Read', 'is_published' => true]);
        Article::factory()->draft()->create(['title' => 'Draft Read']);

        $this->get(route('health.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('health/index')
                ->has('articles', 1)
                ->where('articles.0.title', 'Published Read'));
    }

    public function test_health_index_filters_by_category(): void
    {
        Article::factory()->create(['category' => 'Nutrition', 'is_published' => true]);
        Article::factory()->create(['category' => 'Healthy Habits', 'is_published' => true]);

        $this->get(route('health.index', ['category' => 'Nutrition']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('health/index')
                ->has('articles', 1)
                ->where('activeCategory', 'Nutrition'));
    }

    public function test_health_show_renders_article_with_related(): void
    {
        $article = Article::factory()->create(['is_published' => true, 'category' => 'Nutrition']);
        Article::factory()->create(['is_published' => true, 'category' => 'Nutrition']);
        Article::factory()->draft()->create(['category' => 'Nutrition']);

        $this->get(route('health.show', $article))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('health/show')
                ->where('article.slug', $article->slug)
                ->has('related', 1));
    }

    public function test_health_show_returns_404_for_draft(): void
    {
        $article = Article::factory()->draft()->create();

        $this->get(route('health.show', $article))->assertNotFound();
    }

    public function test_health_article_body_is_rendered_as_html(): void
    {
        $article = Article::factory()->create([
            'is_published' => true,
            'body' => '<p>First paragraph.</p><p>Second paragraph.</p>',
        ]);

        $this->get(route('health.show', $article))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('health/show')
                ->where('article.body_html', '<p>First paragraph.</p><p>Second paragraph.</p>')
                ->missing('article.body'));
    }

    public function test_viewing_a_published_article_increments_views_once_per_session(): void
    {
        $article = Article::factory()->create(['is_published' => true]);
        $updatedAt = $article->updated_at->toDateTimeString();

        $this->get(route('health.show', $article))->assertOk();
        $this->get(route('health.show', $article))->assertOk();

        $article->refresh();

        $this->assertSame(1, $article->views);
        $this->assertSame($updatedAt, $article->updated_at->toDateTimeString());
    }

    public function test_viewing_a_draft_does_not_increment_views(): void
    {
        $article = Article::factory()->draft()->create();

        $this->get(route('health.show', $article))->assertNotFound();

        $this->assertSame(0, $article->fresh()->views);
    }
}
