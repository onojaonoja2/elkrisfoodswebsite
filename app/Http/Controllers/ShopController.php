<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::query()
            ->with('category:id,name,slug')
            ->where('is_published', true);

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', fn ($qc) => $qc->where('slug', $category));
        }

        if ($pack = $request->query('pack')) {
            $query->whereHas('variants', fn ($qv) => $qv->where('pack_type', $pack)->where('is_available', true));
        }

        match ($request->query('sort')) {
            'name' => $query->orderBy('name'),
            default => $query->orderBy('sort_order'),
        };

        return Inertia::render('shop/index', [
            'products' => $query->get(),
            'categories' => Category::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'filters' => [
                'q' => $request->query('q'),
                'category' => $request->query('category'),
                'pack' => $request->query('pack'),
                'sort' => $request->query('sort'),
            ],
            'settings' => Setting::publicMap(),
        ]);
    }
}
