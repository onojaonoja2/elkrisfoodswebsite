<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->query('category');

        return Inertia::render('gallery/index', [
            'items' => GalleryItem::query()
                ->published()
                ->when($category, fn ($q) => $q->where('category', $category))
                ->orderBy('sort_order')
                ->get(['id', 'title', 'caption', 'category', 'media_type', 'media_path']),
            'categories' => GalleryItem::query()
                ->published()
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'videoCount' => GalleryItem::query()->published()->where('media_type', GalleryItem::MEDIA_VIDEO)->count(),
            'activeCategory' => $category,
            'settings' => Setting::publicMap(),
        ]);
    }
}
