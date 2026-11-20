<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\ContactLeads\ContactLeadResource;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\Article;
use App\Models\ContactLead;
use App\Models\DistributorLead;
use App\Models\GalleryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $newLeads = ContactLead::query()->where('status', 'new')->count()
            + DistributorLead::query()->where('status', 'new')->count();

        return [
            Stat::make('Published products', Product::query()->where('is_published', true)->count())
                ->description('Live in the catalogue')
                ->descriptionIcon(Heroicon::OutlinedShoppingBag)
                ->color('success')
                ->url(ProductResource::getUrl()),
            Stat::make('Published articles', Article::query()->where('is_published', true)->count())
                ->description('Health Hub stories')
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('primary')
                ->url(ArticleResource::getUrl()),
            Stat::make('Gallery items', GalleryItem::query()->where('is_published', true)->count())
                ->description('Photos and videos')
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('success')
                ->url(GalleryItemResource::getUrl()),
            Stat::make('New leads', $newLeads)
                ->description('Contact and distributor')
                ->descriptionIcon(Heroicon::OutlinedInbox)
                ->color('warning')
                ->url(ContactLeadResource::getUrl()),
            Stat::make('Pending orders', Order::query()->where('status', Order::STATUS_PENDING)->count())
                ->description('Awaiting confirmation')
                ->descriptionIcon(Heroicon::OutlinedShoppingCart)
                ->color('warning')
                ->url(OrderResource::getUrl()),
            Stat::make('Subscribers', Subscriber::query()->whereNull('unsubscribed_at')->count())
                ->description('Active newsletter signups')
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->color('info')
                ->url(SubscriberResource::getUrl()),
        ];
    }
}
