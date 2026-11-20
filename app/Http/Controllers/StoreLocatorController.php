<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreLocatorController extends Controller
{
    public function index(Request $request): Response
    {
        $state = $request->query('state');
        $city = $request->query('city');

        $stores = Store::query()
            ->where('is_published', true)
            ->filter($state, $city)
            ->orderBy('state')
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->map(fn (Store $store) => $store->only([
                'id', 'name', 'address', 'city', 'state', 'phone',
                'opening_hours', 'latitude', 'longitude', 'products_available',
            ]) + ['directions_url' => $store->directionsUrl()]);

        $cities = Store::query()
            ->where('is_published', true)
            ->when($state, fn ($q) => $q->where('state', $state))
            ->orderBy('city')
            ->distinct()
            ->pluck('city');

        return Inertia::render('stores/index', [
            'stores' => $stores,
            'states' => Store::states(),
            'cities' => $cities,
            'filters' => ['state' => $state, 'city' => $city],
            'settings' => Setting::publicMap(),
        ]);
    }
}
