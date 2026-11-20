<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $entries = Cache::remember(
            'sitemap.entries',
            now()->addMinutes(30),
            fn (): array => $this->entries(),
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($entries as $entry) {
            $xml .= '    <url>';
            $xml .= '<loc>'.e($entry['loc']).'</loc>';

            if ($entry['lastmod'] !== null) {
                $xml .= '<lastmod>'.$entry['lastmod'].'</lastmod>';
            }

            $xml .= '</url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /orders',
            'Disallow: /settings',
            'Disallow: /dashboard',
            'Disallow: /search',
            '',
            'Sitemap: '.route('sitemap.index'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * @return array<int, array{loc: string, lastmod: ?string}>
     */
    private function entries(): array
    {
        $entries = [];

        foreach ([
            'home',
            'about',
            'contact.index',
            'distributor.create',
            'stores.index',
            'shop.index',
            'products.index',
            'recipes.index',
            'health.index',
            'gallery.index',
        ] as $routeName) {
            $entries[] = ['loc' => route($routeName), 'lastmod' => null];
        }

        Product::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Product $product) use (&$entries): void {
                $entries[] = [
                    'loc' => route('products.show', $product),
                    'lastmod' => $product->updated_at?->format('Y-m-d'),
                ];
            });

        Recipe::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Recipe $recipe) use (&$entries): void {
                $entries[] = [
                    'loc' => route('recipes.show', $recipe),
                    'lastmod' => $recipe->updated_at?->format('Y-m-d'),
                ];
            });

        Article::query()
            ->published()
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Article $article) use (&$entries): void {
                $entries[] = [
                    'loc' => route('health.show', $article),
                    'lastmod' => $article->updated_at?->format('Y-m-d'),
                ];
            });

        return $entries;
    }
}
