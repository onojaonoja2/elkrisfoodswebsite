<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Site Settings';

    protected string $view = 'filament.pages.settings';

    /**
     * @var array<string, string|null>
     */
    public ?array $data = [];

    /**
     * @return array<string, string>
     */
    private static function keyMap(): array
    {
        return [
            'brand_primary_tagline' => 'brand.primary_tagline',
            'brand_positioning_tagline' => 'brand.positioning_tagline',
            'brand_positioning_line' => 'brand.positioning_line',
            'contact_email' => 'contact.email',
            'contact_phone' => 'contact.phone',
            'contact_address' => 'contact.address',
            'contact_whatsapp' => 'contact.whatsapp',
            'delivery_info' => 'delivery.info',
            'social_facebook' => 'social.facebook',
            'social_instagram' => 'social.instagram',
            'social_tiktok' => 'social.tiktok',
            'social_youtube' => 'social.youtube',
            'social_x' => 'social.x',
            'analytics_ga4_id' => 'analytics.ga4_id',
            'analytics_meta_pixel_id' => 'analytics.meta_pixel_id',
            'analytics_google_ads_id' => 'analytics.google_ads_id',
            'analytics_search_console_verification' => 'analytics.search_console_verification',
            'analytics_meta_capi_token' => 'analytics.meta_capi_token',
            'analytics_meta_capi_test_event_code' => 'analytics.meta_capi_test_event_code',
        ];
    }

    public function mount(): void
    {
        $settings = Setting::publicMap();
        $state = [];

        foreach (self::keyMap() as $formKey => $settingKey) {
            $state[$formKey] = $settings[$settingKey] ?? Setting::get($settingKey, '');
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Brand messaging')
                        ->schema([
                            TextInput::make('brand_primary_tagline')
                                ->label('Primary tagline')
                                ->required(),
                            TextInput::make('brand_positioning_tagline')
                                ->label('Positioning tagline')
                                ->required(),
                            TextInput::make('brand_positioning_line')
                                ->label('Footer line')
                                ->required(),
                        ])->columns(2),

                    Section::make('Contact')
                        ->schema([
                            TextInput::make('contact_email')
                                ->label('Email')
                                ->email()
                                ->required(),
                            TextInput::make('contact_phone')
                                ->label('Phone')
                                ->tel(),
                            TextInput::make('contact_whatsapp')
                                ->label('WhatsApp number')
                                ->tel()
                                ->helperText('International format, e.g. 2348012345678. Leave blank to hide WhatsApp CTAs.'),
                            TextInput::make('contact_address')
                                ->label('Address')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Section::make('Delivery')
                        ->schema([
                            Textarea::make('delivery_info')
                                ->label('Delivery information')
                                ->rows(4)
                                ->helperText('Shown near order buttons on product and shop pages. Leave blank to hide.'),
                        ]),

                    Section::make('Social links')
                        ->schema([
                            TextInput::make('social_facebook')
                                ->label('Facebook')
                                ->url()
                                ->placeholder('https://facebook.com/...'),
                            TextInput::make('social_instagram')
                                ->label('Instagram')
                                ->url()
                                ->placeholder('https://instagram.com/...'),
                            TextInput::make('social_tiktok')
                                ->label('TikTok')
                                ->url()
                                ->placeholder('https://tiktok.com/@...'),
                            TextInput::make('social_youtube')
                                ->label('YouTube')
                                ->url()
                                ->placeholder('https://youtube.com/@...'),
                            TextInput::make('social_x')
                                ->label('X (Twitter)')
                                ->url()
                                ->placeholder('https://x.com/...'),
                        ])->columns(2),

                    Section::make('Analytics')
                        ->schema([
                            TextInput::make('analytics_ga4_id')
                                ->label('Google Analytics 4 ID')
                                ->placeholder('G-XXXXXXXXXX'),
                            TextInput::make('analytics_meta_pixel_id')
                                ->label('Meta Pixel ID'),
                            TextInput::make('analytics_google_ads_id')
                                ->label('Google Ads ID')
                                ->placeholder('AW-XXXXXXXXXX')
                                ->helperText('Remarketing / retargeting tag.'),
                            TextInput::make('analytics_search_console_verification')
                                ->label('Search Console verification')
                                ->helperText('Google site verification meta token. Leave blank if not set up.'),
                        ])->columns(2),

                    Section::make('Meta Conversions API (server-side)')
                        ->schema([
                            TextInput::make('analytics_meta_capi_token')
                                ->label('Access token')
                                ->type('password')
                                ->autocomplete('new-password')
                                ->helperText('Server-side only. Never sent to the browser. Leave blank to disable Conversions API events.'),
                            TextInput::make('analytics_meta_capi_test_event_code')
                                ->label('Test event code (optional)')
                                ->helperText('Meta test_event_code while validating events in Events Manager.'),
                        ])->columns(2),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save settings')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (self::keyMap() as $formKey => $settingKey) {
            Setting::set($settingKey, $data[$formKey] ?? null);
        }

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('Public site settings have been updated.')
            ->send();
    }
}
