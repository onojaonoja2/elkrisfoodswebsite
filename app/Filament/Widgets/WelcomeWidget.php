<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteContent;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\Products\ProductResource;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -20;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'name' => auth()->user()?->name ?? 'there',
            'contentUrl' => SiteContent::getUrl(),
            'productUrl' => ProductResource::getUrl('create'),
            'articleUrl' => ArticleResource::getUrl('create'),
            'galleryUrl' => GalleryItemResource::getUrl('create'),
            'siteUrl' => url('/'),
        ];
    }
}
