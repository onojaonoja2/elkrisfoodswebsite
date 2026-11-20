<?php

namespace App\Http\Controllers;

use App\Concerns\BuildsStructuredData;
use App\Models\Article;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class HealthController extends Controller
{
    use BuildsStructuredData;

    public function index(Request $request): Response
    {
        $category = $request->query('category');

        return Inertia::render('health/index', [
            'articles' => Article::query()
                ->published()
                ->when($category, fn ($q) => $q->where('category', $category))
                ->latest('published_at')
                ->get(['id', 'title', 'slug', 'category', 'excerpt', 'cover_path', 'author', 'reading_minutes', 'published_at']),
            'categories' => Article::query()
                ->published()
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'activeCategory' => $category,
            'settings' => Setting::publicMap(),
        ]);
    }

    public function show(Request $request, Article $article): Response
    {
        abort_unless($article->is_published, 404);

        $this->recordView($article, $request);

        return Inertia::render('health/show', [
            'article' => $article->append('body_html')->makeHidden('body'),
            'related' => Article::query()
                ->published()
                ->whereKeyNot($article->id)
                ->where('category', $article->category)
                ->latest('published_at')
                ->limit(3)
                ->get(['id', 'title', 'slug', 'category', 'excerpt', 'cover_path', 'reading_minutes', 'published_at']),
            'settings' => Setting::publicMap(),
            'jsonLd' => [$this->articleSchema($article)],
        ]);
    }

    /**
     * Count one view per browser session without changing the article's timestamps.
     */
    private function recordView(Article $article, Request $request): void
    {
        $sessionKey = "articles.viewed.{$article->getKey()}";

        if ($request->session()->has($sessionKey)) {
            return;
        }

        DB::table($article->getTable())
            ->where('id', $article->getKey())
            ->increment('views');

        $request->session()->put($sessionKey, true);
    }
}
