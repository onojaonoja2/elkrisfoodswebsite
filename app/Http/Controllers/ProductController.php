<?php

namespace App\Http\Controllers;

use App\Concerns\BuildsStructuredData;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use BuildsStructuredData;

    public function index(Request $request): Response
    {
        $category = $request->query('category');

        return Inertia::render('products/index', [
            'products' => Product::query()
                ->with(['category:id,name,slug', 'variants', 'mealTypes:id,name,slug,usage_copy'])
                ->where('is_published', true)
                ->when($category, fn ($q) => $q->whereHas('category', fn ($qc) => $qc->where('slug', $category)))
                ->orderBy('sort_order')
                ->get(),
            'categories' => Category::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'activeCategory' => $category,
            'settings' => Setting::publicMap(),
        ]);
    }

    public function show(Product $product): Response
    {
        abort_unless($product->is_published, 404);

        $product->load(['category:id,name,slug', 'variants', 'mealTypes:id,name,slug,usage_copy', 'recipes' => fn ($q) => $q->where('is_published', true)->limit(3)]);

        $rating = $product->reviews()
            ->approved()
            ->toBase()
            ->selectRaw('AVG(rating) as average, COUNT(*) as count')
            ->first();

        return Inertia::render('products/show', [
            'product' => $product,
            'settings' => Setting::publicMap(),
            'reviews' => $product->reviews()
                ->approved()
                ->latest()
                ->get(['id', 'name', 'rating', 'body', 'created_at']),
            'ratingSummary' => [
                'average' => $rating?->average !== null ? round((float) $rating->average, 1) : 0.0,
                'count' => (int) ($rating?->count ?? 0),
            ],
            'jsonLd' => [$this->productSchema(
                $product,
                $rating?->average !== null ? round((float) $rating->average, 1) : null,
                (int) ($rating?->count ?? 0),
            )],
        ]);
    }
}
