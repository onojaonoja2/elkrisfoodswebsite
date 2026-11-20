<?php

namespace App\Providers;

use App\Livewire\GenerateSignedUploadUrl;
use App\Support\Media;
use App\Support\SafeHtmlSanitizer;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl as LivewireGenerateSignedUploadUrl;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LivewireGenerateSignedUploadUrl::class, GenerateSignedUploadUrl::class);

        $this->app->scoped(HtmlSanitizerConfig::class, fn (): HtmlSanitizerConfig => (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowAttribute('class', allowedElements: '*')
            ->allowAttribute('data-color', allowedElements: '*')
            ->allowAttribute('data-cols', allowedElements: '*')
            ->allowAttribute('data-col-span', allowedElements: '*')
            ->allowAttribute('data-from-breakpoint', allowedElements: '*')
            ->allowAttribute('data-id', allowedElements: '*')
            ->allowAttribute('data-type', allowedElements: '*')
            ->allowAttribute('style', allowedElements: '*')
            ->allowAttribute('width', allowedElements: 'img')
            ->allowAttribute('height', allowedElements: 'img')
            ->allowElement('iframe', [
                'src',
                'title',
                'loading',
                'allow',
                'allowfullscreen',
            ])
            ->withMaxInputLength(500000));

        $this->app->scoped(HtmlSanitizerInterface::class, fn (): HtmlSanitizerInterface => new SafeHtmlSanitizer(
            new HtmlSanitizer($this->app->make(HtmlSanitizerConfig::class)),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        FileUpload::configureUsing(function (FileUpload $component): void {
            $component
                ->disk('media')
                ->formatStateUsing(fn ($state) => is_array($state)
                    ? Media::paths($state)
                    : Media::path($state));
        });

        ImageColumn::configureUsing(function (ImageColumn $column): void {
            $column->disk('media');
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
