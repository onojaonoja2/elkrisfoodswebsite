<?php

namespace App\Http\Controllers;

use App\Concerns\BuildsStructuredData;
use App\Models\Recipe;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    use BuildsStructuredData;

    public function index(Request $request): Response
    {
        $category = $request->query('category');

        return Inertia::render('recipes/index', [
            'recipes' => Recipe::query()
                ->with('product:id,name,slug,hero_image')
                ->where('is_published', true)
                ->when($category, fn ($q) => $q->where('category', $category))
                ->latest()
                ->get(['id', 'title', 'slug', 'category', 'image_path', 'prep_minutes', 'difficulty', 'summary', 'product_id']),
            'categories' => Recipe::query()
                ->where('is_published', true)
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'activeCategory' => $category,
            'settings' => Setting::publicMap(),
        ]);
    }

    public function show(Recipe $recipe): Response
    {
        abort_unless($recipe->is_published, 404);

        $recipe->load('product:id,name,slug,hero_image,whatsapp');

        return Inertia::render('recipes/show', [
            'recipe' => $recipe,
            'related' => Recipe::query()
                ->with('product:id,name,slug,hero_image')
                ->where('is_published', true)
                ->whereKeyNot($recipe->id)
                ->where(fn ($q) => $q->where('category', $recipe->category)
                    ->orWhere('product_id', $recipe->product_id))
                ->inRandomOrder()
                ->limit(3)
                ->get(['id', 'title', 'slug', 'category', 'image_path', 'prep_minutes', 'difficulty', 'summary', 'product_id']),
            'settings' => Setting::publicMap(),
            'jsonLd' => [$this->recipeSchema($recipe)],
        ]);
    }
}
