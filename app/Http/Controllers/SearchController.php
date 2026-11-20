<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $query = trim((string) $request->query('q', ''));
        $term = mb_substr($query, 0, 100);

        $results = collect();

        if (mb_strlen($term) >= 2) {
            $like = '%'.$term.'%';

            $results = $results->merge(
                Product::query()
                    ->where('is_published', true)
                    ->where(fn ($q) => $q->where('name', 'like', $like)
                        ->orWhere('tagline', 'like', $like)
                        ->orWhere('description', 'like', $like))
                    ->limit(8)
                    ->get(['id', 'name', 'slug', 'tagline', 'hero_image'])
                    ->map(fn ($product) => [
                        'type' => 'product',
                        'title' => $product->name,
                        'subtitle' => $product->tagline,
                        'url' => route('products.show', $product),
                    ])
            );

            $results = $results->merge(
                Recipe::query()
                    ->where('is_published', true)
                    ->where(fn ($q) => $q->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('category', 'like', $like))
                    ->limit(8)
                    ->get(['id', 'title', 'slug', 'category', 'summary'])
                    ->map(fn ($recipe) => [
                        'type' => 'recipe',
                        'title' => $recipe->title,
                        'subtitle' => $recipe->category,
                        'url' => route('recipes.show', $recipe),
                    ])
            );

            $results = $results->merge(
                Article::query()
                    ->published()
                    ->where(fn ($q) => $q->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('category', 'like', $like))
                    ->limit(8)
                    ->get(['id', 'title', 'slug', 'category', 'excerpt'])
                    ->map(fn ($article) => [
                        'type' => 'article',
                        'title' => $article->title,
                        'subtitle' => $article->category,
                        'url' => route('health.show', $article),
                    ])
            );

            $results = $results->merge(
                Store::query()
                    ->where('is_published', true)
                    ->where(fn ($q) => $q->where('name', 'like', $like)
                        ->orWhere('city', 'like', $like)
                        ->orWhere('state', 'like', $like)
                        ->orWhere('address', 'like', $like))
                    ->limit(8)
                    ->get(['id', 'name', 'city', 'state'])
                    ->map(fn ($store) => [
                        'type' => 'store',
                        'title' => $store->name,
                        'subtitle' => trim($store->city.', '.$store->state, ', '),
                        'url' => route('stores.index'),
                    ])
            );
        }

        return Inertia::render('search/index', [
            'q' => $term,
            'results' => $results->values()->all(),
            'total' => $results->count(),
            'settings' => Setting::publicMap(),
        ]);
    }
}
