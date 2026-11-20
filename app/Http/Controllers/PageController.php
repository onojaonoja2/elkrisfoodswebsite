<?php

namespace App\Http\Controllers;

use App\Concerns\BuildsStructuredData;
use App\Models\Category;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\MealType;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    use BuildsStructuredData;

    public function home(): Response
    {
        $faqs = Faq::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'question', 'answer', 'category']);

        $videos = GalleryItem::query()
            ->published()
            ->where('media_type', GalleryItem::MEDIA_VIDEO)
            ->orderBy('sort_order')
            ->limit(3)
            ->get(['id', 'title', 'caption', 'media_path']);

        return Inertia::render('home', [
            'featuredProducts' => Product::query()
                ->with('category')
                ->where('is_published', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
            'categories' => Category::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'image_path', 'description']),
            'mealTypes' => MealType::query()
                ->with('products:id,name,slug,hero_image,category_id,best_with')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(),
            'videos' => $videos,
            'heroVideo' => $videos->first()?->media_url ?? '/media/videos/brand-positioning.mp4',
            'recipes' => Recipe::query()
                ->with('product:id,name,slug')
                ->where('is_published', true)
                ->latest()
                ->limit(3)
                ->get(['id', 'title', 'slug', 'category', 'image_path', 'prep_minutes', 'difficulty', 'summary', 'product_id']),
            'testimonials' => Testimonial::query()
                ->where('is_published', true)
                ->orderByDesc('is_featured')
                ->limit(6)
                ->get(),
            'nearbyStores' => Store::query()
                ->where('is_published', true)
                ->orderBy('state')
                ->limit(4)
                ->get(['id', 'name', 'city', 'state', 'address', 'phone']),
            'faqs' => $faqs,
            'settings' => Setting::publicMap(),
            'jsonLd' => $faqs->isNotEmpty() ? [$this->faqSchema($faqs)] : [],
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('about', [
            'settings' => Setting::publicMap(),
            'about' => Setting::aboutContent(),
        ]);
    }
}
