<?php

namespace App\Filament\Content\Widgets;

use App\Filament\Pages\SiteContent;
use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();

        $articles = Article::query()
            ->when($user?->isAuthor(), fn ($query) => $query->where('author_id', $user->id));

        $stats = [
            Stat::make('Published articles', (clone $articles)->where('is_published', true)->count())
                ->description('Live on the Health Hub')
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('success'),
            Stat::make('Drafts', (clone $articles)->where('is_published', false)->count())
                ->description('Waiting to be published')
                ->descriptionIcon(Heroicon::OutlinedPencilSquare)
                ->color('warning'),
            Stat::make('Write an article', 'Create')
                ->description('Open the article editor')
                ->descriptionIcon(Heroicon::OutlinedPlusCircle)
                ->url(ArticleResource::getUrl('create')),
        ];

        if ($user?->canManageSiteContent()) {
            $stats[] = Stat::make('Website content', 'Edit')
                ->description('Update public page copy')
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->url(SiteContent::getUrl());
        }

        return $stats;
    }
}
