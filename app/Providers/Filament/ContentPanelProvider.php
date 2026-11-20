<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\SiteContent;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ContentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('content')
            ->path('studio')
            ->login()
            ->profile()
            ->colors([
                'primary' => Color::hex('#d62828'),
                'success' => Color::hex('#1b7a36'),
                'warning' => Color::hex('#e9a31a'),
                'danger' => Color::hex('#a61b1b'),
                'info' => Color::hex('#279143'),
            ])
            ->brandName('Elkris Content Studio')
            ->brandLogo(asset('media/logo/Elkris_logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.svg'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->resources([
                ArticleResource::class,
            ])
            ->pages([
                Dashboard::class,
                SiteContent::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Content/Widgets'), for: 'App\Filament\Content\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
