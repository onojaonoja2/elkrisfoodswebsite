<?php

namespace Tests\Feature;

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Models\Article;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_access_the_content_studio_and_all_content_pages(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/studio')->assertOk();
        $this->actingAs($editor)->get('/studio/articles')->assertOk();
        $this->actingAs($editor)->get('/studio/articles/create')->assertOk();
        $this->actingAs($editor)->get('/studio/site-content')->assertOk();
    }

    public function test_author_can_write_articles_but_cannot_manage_site_content(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->get('/studio')->assertOk();
        $this->actingAs($author)->get('/studio/articles')->assertOk();
        $this->actingAs($author)->get('/studio/articles/create')->assertOk();
        $this->actingAs($author)->get('/studio/site-content')->assertForbidden();
        $this->actingAs($author)->get('/admin')->assertForbidden();
    }

    public function test_customer_cannot_access_the_content_studio(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/studio')->assertForbidden();
        $this->actingAs($customer)->get('/studio/articles')->assertForbidden();
    }

    public function test_admin_can_access_both_panels(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/studio')->assertOk();
        $this->actingAs($admin)->get('/studio/site-content')->assertOk();
    }

    public function test_author_cannot_open_another_authors_article_for_editing(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();

        $ownArticle = Article::factory()->create(['author_id' => $author->id]);
        $otherArticle = Article::factory()->create(['author_id' => $otherAuthor->id]);

        $this->actingAs($author)->get('/studio/articles/'.$ownArticle->id.'/edit')->assertOk();
        $this->actingAs($author)->get('/studio/articles/'.$otherArticle->id.'/edit')->assertNotFound();
    }

    public function test_editor_can_open_another_authors_article_for_editing(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        $this->actingAs($editor)->get('/studio/articles/'.$article->id.'/edit')->assertOk();
    }

    public function test_creating_an_article_records_the_author_and_defaults_the_byline(): void
    {
        $author = User::factory()->author()->create();

        Filament::setCurrentPanel(Filament::getPanel('content'));

        $this->actingAs($author);

        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'Fibre for Everyday Meals',
                'slug' => 'fibre-for-everyday-meals',
                'category' => Article::CATEGORIES[0],
                'reading_minutes' => 4,
                'body' => '<p>A short article body.</p>',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $article = Article::query()->where('slug', 'fibre-for-everyday-meals')->firstOrFail();

        $this->assertSame($author->id, $article->author_id);
        $this->assertSame($author->name, $article->author);
        $this->assertSame('<p>A short article body.</p>', $article->body);
    }

    public function test_author_can_save_an_article_as_a_draft(): void
    {
        $author = User::factory()->author()->create();

        Filament::setCurrentPanel(Filament::getPanel('content'));

        $this->actingAs($author);

        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'Draft Article',
                'slug' => 'draft-article',
                'category' => Article::CATEGORIES[0],
                'reading_minutes' => 4,
                'body' => '<p>A draft body.</p>',
                'is_published' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $article = Article::query()->where('slug', 'draft-article')->firstOrFail();

        $this->assertFalse($article->is_published);
        $this->get(route('health.show', $article))->assertNotFound();
    }

    public function test_author_can_update_their_own_article(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        Filament::setCurrentPanel(Filament::getPanel('content'));

        $this->actingAs($author);

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->fillForm(['title' => 'Updated Article Title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated Article Title', $article->fresh()->title);
    }

    public function test_author_can_delete_their_own_article(): void
    {
        $author = User::factory()->author()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        Filament::setCurrentPanel(Filament::getPanel('content'));

        $this->actingAs($author);

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->callAction('delete');

        $this->assertModelMissing($article);
    }

    public function test_author_cannot_delete_another_authors_article(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        $article = Article::factory()->create(['author_id' => $otherAuthor->id]);

        $this->assertFalse($author->can('delete', $article));
    }
}
