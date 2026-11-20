<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'siteUrl' => config('app.url'),
            'auth' => [
                'user' => $request->user()
                    ? $request->user()->only('id', 'name', 'email')
                    : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'settings' => Setting::publicMap(),
            'content' => [
                'global' => Setting::content('global'),
                'home' => Setting::content('home'),
                'about' => Setting::content('about'),
                'products' => Setting::content('products'),
                'product_detail' => Setting::content('product_detail'),
                'shop' => Setting::content('shop'),
                'recipes' => Setting::content('recipes'),
                'recipe_detail' => Setting::content('recipe_detail'),
                'health' => Setting::content('health'),
                'article_detail' => Setting::content('article_detail'),
                'gallery' => Setting::content('gallery'),
                'stores' => Setting::content('stores'),
                'search' => Setting::content('search'),
                'contact' => Setting::content('contact'),
                'distributor' => Setting::content('distributor'),
                'distributor_status' => Setting::content('distributor_status'),
                'account' => Setting::content('account'),
                'orders' => Setting::content('orders'),
                'labels' => Setting::content('labels'),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'wa_url' => $request->session()->get('wa_url'),
            ],
        ];
    }
}
