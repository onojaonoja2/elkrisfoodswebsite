<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreLocatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::permanentRedirect('/products/elkris-plain-flour', '/products/elkris-premium-flours');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product:slug}/reviews', [ReviewController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('reviews.store');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');

Route::get('/health', [HealthController::class, 'index'])->name('health.index');
Route::get('/health/{article:slug}', [HealthController::class, 'show'])->name('health.show');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/search', [SearchController::class, 'index'])
    ->middleware('throttle:30,1')
    ->name('search.index');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');

Route::get('/where-to-buy', [StoreLocatorController::class, 'index'])->name('stores.index');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/distributor', [DistributorController::class, 'create'])->name('distributor.create');
Route::post('/distributor', [DistributorController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('distributor.store');
Route::get('/distributor/status', [DistributorController::class, 'status'])
    ->middleware('throttle:30,1')
    ->name('distributor.status');

Route::get('/orders/{number}', [OrderController::class, 'show'])
    ->middleware('throttle:20,1')
    ->name('orders.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/account/orders/{number}', [AccountController::class, 'order'])->name('account.orders');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('/dashboard', 'dashboard')->name('dashboard');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

require __DIR__.'/settings.php';
