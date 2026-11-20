<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class SiteContent extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Website Content';

    protected static ?string $title = 'Website Content';

    protected string $view = 'filament.pages.site-content';

    public static function canAccess(): bool
    {
        return auth()->user()?->canManageSiteContent() ?? false;
    }

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $about = Setting::aboutContent();

        $this->form->fill([
            'global' => Setting::content('global'),
            'home' => Setting::content('home'),
            'about' => Setting::content('about'),
            'about_body' => [
                'who_we_are' => collect($about['who_we_are'])
                    ->map(fn (string $paragraph): array => ['paragraph' => $paragraph])
                    ->all(),
                'vision' => $about['vision'],
                'mission' => $about['mission'],
                'values_intro' => $about['values_intro'],
                'core_values' => $about['core_values'],
            ],
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
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('Website content')
                        ->tabs([
                            Tab::make('Global')
                                ->icon(Heroicon::OutlinedGlobeAlt)
                                ->schema([
                                    Section::make('Brand')
                                        ->schema([
                                            TextInput::make('global.brand.site_name')->required(),
                                            TextInput::make('global.brand.logo')->helperText('Public path or full URL.'),
                                            TextInput::make('global.brand.favicon')->helperText('Public path or full URL.'),
                                            TextInput::make('global.brand.apple_touch_icon')->helperText('Public path or full URL.'),
                                            TextInput::make('global.brand.default_og_image')->helperText('Default social sharing image.'),
                                            Textarea::make('global.brand.default_description')->rows(3)->columnSpanFull(),
                                        ])->columns(2),
                                    Section::make('Header navigation')
                                        ->schema([
                                            Repeater::make('global.nav.items')
                                                ->label('Navigation links')
                                                ->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('url')->required()->helperText('Internal path such as /products or a full URL.'),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel('Add navigation link')
                                                ->reorderable()
                                                ->defaultItems(0),
                                            TextInput::make('global.nav.account_label'),
                                            TextInput::make('global.nav.search_label'),
                                            TextInput::make('global.nav.shop_label'),
                                            TextInput::make('global.nav.theme_light_label'),
                                            TextInput::make('global.nav.theme_dark_label'),
                                            TextInput::make('global.nav.menu_open_label'),
                                            TextInput::make('global.nav.menu_close_label'),
                                        ])->columns(2),
                                    Section::make('Footer')
                                        ->schema([
                                            Repeater::make('global.footer.columns')
                                                ->label('Footer columns')
                                                ->schema([
                                                    TextInput::make('title')->required(),
                                                    Repeater::make('links')
                                                        ->schema([
                                                            TextInput::make('label')->required(),
                                                            TextInput::make('url')->required(),
                                                        ])
                                                        ->columns(2)
                                                        ->addActionLabel('Add footer link')
                                                        ->defaultItems(0),
                                                ])
                                                ->addActionLabel('Add footer column')
                                                ->reorderable()
                                                ->defaultItems(0),
                                            TextInput::make('global.footer.follow_heading'),
                                            TextInput::make('global.footer.tagline_fallback'),
                                            TextInput::make('global.footer.positioning_fallback'),
                                            TextInput::make('global.footer.copyright')->helperText('Use {year} for the current year.'),
                                        ])->columns(2),
                                    Section::make('Shared copy')
                                        ->schema([
                                            TextInput::make('global.newsletter.heading'),
                                            Textarea::make('global.newsletter.body')->rows(2),
                                            TextInput::make('global.newsletter.placeholder'),
                                            TextInput::make('global.newsletter.button'),
                                            TextInput::make('global.newsletter.processing'),
                                            TextInput::make('global.newsletter.success_heading'),
                                            Textarea::make('global.newsletter.success_body')->rows(2),
                                            TextInput::make('global.whatsapp.fab_label'),
                                            Textarea::make('global.whatsapp.general_message')->rows(2),
                                            Textarea::make('global.whatsapp.order_message')->rows(2),
                                            TextInput::make('global.share.label'),
                                            TextInput::make('global.share.copied'),
                                            TextInput::make('global.share.copy'),
                                        ])->columns(2),
                                    Section::make('Product cards')
                                        ->schema([
                                            TextInput::make('global.product_card.no_image'),
                                            TextInput::make('global.product_card.view'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Homepage')
                                ->icon(Heroicon::OutlinedHome)
                                ->schema([
                                    Section::make('SEO')
                                        ->schema([
                                            TextInput::make('home.seo.title'),
                                            Textarea::make('home.seo.description')->rows(2),
                                        ])->columns(2),
                                    Section::make('Hero carousel')
                                        ->schema([
                                            Repeater::make('home.hero.slides')
                                                ->label('Slides')
                                                ->schema([
                                                    TextInput::make('eyebrow'),
                                                    TextInput::make('title')->required(),
                                                    TextInput::make('highlight'),
                                                    Textarea::make('body')->rows(2),
                                                    TextInput::make('body_class')->helperText('Optional Tailwind classes for the body text.'),
                                                    TextInput::make('cta_label'),
                                                    TextInput::make('cta_url')->helperText('Internal path or full URL.'),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel('Add slide')
                                                ->reorderable()
                                                ->defaultItems(0),
                                            TextInput::make('home.hero.aria_label'),
                                            TextInput::make('home.hero.watch_label'),
                                            TextInput::make('home.hero.previous_label'),
                                            TextInput::make('home.hero.next_label'),
                                            TextInput::make('home.hero.slide_label'),
                                        ])->columns(2),
                                    Section::make('Trust bar')
                                        ->schema([
                                            TagsInput::make('home.trust_items')
                                                ->placeholder('Add a trust item and press enter')
                                                ->columnSpanFull(),
                                        ]),
                                    Section::make('Section headings and calls to action')
                                        ->schema([
                                            TextInput::make('home.sections.products_eyebrow'),
                                            TextInput::make('home.sections.products_heading'),
                                            TextInput::make('home.sections.products_cta'),
                                            TextInput::make('home.sections.categories_heading'),
                                            TextInput::make('home.sections.categories_intro'),
                                            TextInput::make('home.sections.discover_eyebrow'),
                                            TextInput::make('home.sections.discover_heading'),
                                            TextInput::make('home.sections.meal_builder_eyebrow'),
                                            TextInput::make('home.sections.meal_builder_heading'),
                                            TextInput::make('home.sections.meal_builder_intro'),
                                            TextInput::make('home.sections.meal_builder_empty'),
                                            TextInput::make('home.sections.why_eyebrow'),
                                            TextInput::make('home.sections.why_heading'),
                                            TagsInput::make('home.sections.why_benefits')->columnSpanFull(),
                                            TextInput::make('home.sections.why_note_one'),
                                            TextInput::make('home.sections.why_note_two'),
                                            TextInput::make('home.sections.meal_ideas_eyebrow'),
                                            TextInput::make('home.sections.meal_ideas_heading'),
                                            TextInput::make('home.sections.meal_ideas_cta'),
                                            TextInput::make('home.sections.watch_eyebrow'),
                                            TextInput::make('home.sections.watch_heading'),
                                            TextInput::make('home.sections.watch_cta'),
                                            TextInput::make('home.sections.stories_eyebrow'),
                                            TextInput::make('home.sections.stories_heading'),
                                            TextInput::make('home.sections.stores_eyebrow'),
                                            TextInput::make('home.sections.stores_heading'),
                                            TextInput::make('home.sections.stores_cta'),
                                            TextInput::make('home.sections.shop_heading'),
                                            Textarea::make('home.sections.shop_body')->rows(2),
                                            TextInput::make('home.sections.shop_cta'),
                                            TextInput::make('home.sections.faq_heading'),
                                        ])->columns(2),
                                    Section::make('Images')
                                        ->schema([
                                            TextInput::make('home.images.why_primary'),
                                            TextInput::make('home.images.why_secondary'),
                                            TextInput::make('home.images.video_poster'),
                                            TextInput::make('home.images.recipe_fallback'),
                                        ])->columns(2),
                                ]),

                            Tab::make('About')
                                ->icon(Heroicon::OutlinedInformationCircle)
                                ->schema([
                                    Section::make('Page header')
                                        ->schema([
                                            TextInput::make('about.seo.title'),
                                            Textarea::make('about.seo.description')->rows(2),
                                            TextInput::make('about.badge'),
                                            TextInput::make('about.heading'),
                                            TextInput::make('about.vision_heading'),
                                            TextInput::make('about.mission_heading'),
                                            TextInput::make('about.values_eyebrow'),
                                            TextInput::make('about.values_heading'),
                                        ])->columns(2),
                                    Section::make('Who we are')
                                        ->schema([
                                            Repeater::make('about_body.who_we_are')
                                                ->label('Paragraphs')
                                                ->schema([
                                                    Textarea::make('paragraph')->rows(3)->required(),
                                                ])
                                                ->addActionLabel('Add paragraph')
                                                ->defaultItems(0),
                                            Textarea::make('about_body.vision')->rows(3),
                                            Textarea::make('about_body.mission')->rows(3),
                                            Textarea::make('about_body.values_intro')->rows(3),
                                            Repeater::make('about_body.core_values')
                                                ->label('Core values')
                                                ->schema([
                                                    TextInput::make('title')->required(),
                                                    Select::make('icon')->options(config('about.icons'))->searchable()->required(),
                                                    Textarea::make('description')->rows(3)->required()->columnSpanFull(),
                                                ])
                                                ->columns(2)
                                                ->addActionLabel('Add core value')
                                                ->defaultItems(0),
                                        ]),
                                ]),

                            Tab::make('Products & Shop')
                                ->icon(Heroicon::OutlinedShoppingBag)
                                ->schema([
                                    Section::make('Products index')
                                        ->schema([
                                            TextInput::make('products.seo.title'),
                                            Textarea::make('products.seo.description')->rows(2),
                                            TextInput::make('products.eyebrow'),
                                            TextInput::make('products.heading'),
                                            Textarea::make('products.intro')->rows(2),
                                            TextInput::make('products.all'),
                                            TextInput::make('products.empty'),
                                            TextInput::make('products.empty_link'),
                                            TextInput::make('products.comparison_eyebrow'),
                                            TextInput::make('products.comparison_heading'),
                                            TextInput::make('products.table_product'),
                                            TextInput::make('products.table_great_for'),
                                            TextInput::make('products.table_benefit'),
                                            TextInput::make('products.table_sizes'),
                                            TextInput::make('products.everyday_cooking'),
                                            TextInput::make('products.dash'),
                                            TextInput::make('products.view_product'),
                                        ])->columns(2),
                                    Section::make('Product detail template')
                                        ->schema([
                                            TextInput::make('product_detail.breadcrumb'),
                                            TextInput::make('product_detail.size_pack'),
                                            TextInput::make('product_detail.carton'),
                                            TextInput::make('product_detail.order_whatsapp'),
                                            TextInput::make('product_detail.view_in_shop'),
                                            TextInput::make('product_detail.great_for'),
                                            TextInput::make('product_detail.nutrition_fallback'),
                                            TextInput::make('product_detail.one_product_many'),
                                            TextInput::make('product_detail.how_to_use'),
                                            TextInput::make('product_detail.perfect_with'),
                                            TextInput::make('product_detail.reviews_heading'),
                                            TextInput::make('product_detail.no_reviews'),
                                            TextInput::make('product_detail.write_review'),
                                            TextInput::make('product_detail.review_name'),
                                            TextInput::make('product_detail.review_email'),
                                            TextInput::make('product_detail.review_rating'),
                                            TextInput::make('product_detail.review_body'),
                                            TextInput::make('product_detail.review_submit'),
                                            TextInput::make('product_detail.usage_fallback'),
                                        ])->columns(2),
                                    Section::make('Shop')
                                        ->schema([
                                            TextInput::make('shop.seo.title'),
                                            Textarea::make('shop.seo.description')->rows(2),
                                            TextInput::make('shop.eyebrow'),
                                            TextInput::make('shop.heading'),
                                            TextInput::make('shop.search_placeholder'),
                                            TextInput::make('shop.search_aria'),
                                            TextInput::make('shop.search_button'),
                                            TextInput::make('shop.all_categories'),
                                            TextInput::make('shop.all_packs'),
                                            TextInput::make('shop.single_pack'),
                                            TextInput::make('shop.carton'),
                                            TextInput::make('shop.sort_featured'),
                                            TextInput::make('shop.sort_name'),
                                            TextInput::make('shop.empty'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Recipes & Health')
                                ->icon(Heroicon::OutlinedBookOpen)
                                ->schema([
                                    Section::make('Recipes index')
                                        ->schema([
                                            TextInput::make('recipes.seo.title'),
                                            Textarea::make('recipes.seo.description')->rows(2),
                                            TextInput::make('recipes.eyebrow'),
                                            TextInput::make('recipes.heading'),
                                            Textarea::make('recipes.intro')->rows(2),
                                            TextInput::make('recipes.all'),
                                            TextInput::make('recipes.empty'),
                                            TextInput::make('recipes.fallback_image'),
                                        ])->columns(2),
                                    Section::make('Recipe detail template')
                                        ->schema([
                                            TextInput::make('recipe_detail.breadcrumb'),
                                            TextInput::make('recipe_detail.minutes'),
                                            TextInput::make('recipe_detail.serves'),
                                            TextInput::make('recipe_detail.ingredients'),
                                            TextInput::make('recipe_detail.preparation'),
                                            TextInput::make('recipe_detail.tips'),
                                            TextInput::make('recipe_detail.product_used'),
                                            TextInput::make('recipe_detail.buy_product'),
                                            TextInput::make('recipe_detail.order_whatsapp'),
                                            TextInput::make('recipe_detail.more_recipes'),
                                            TextInput::make('recipe_detail.all_recipes'),
                                            TextInput::make('recipe_detail.fallback_image'),
                                        ])->columns(2),
                                    Section::make('Health hub')
                                        ->schema([
                                            TextInput::make('health.seo.title'),
                                            Textarea::make('health.seo.description')->rows(2),
                                            TextInput::make('health.eyebrow'),
                                            TextInput::make('health.heading'),
                                            Textarea::make('health.intro')->rows(2),
                                            TextInput::make('health.all'),
                                            TextInput::make('health.empty'),
                                            TextInput::make('health.cta_heading'),
                                            Textarea::make('health.cta_body')->rows(2),
                                            TextInput::make('health.shop_cta'),
                                            TextInput::make('health.recipes_cta'),
                                            TextInput::make('health.fallback_image'),
                                        ])->columns(2),
                                    Section::make('Article detail template')
                                        ->schema([
                                            TextInput::make('article_detail.all_articles'),
                                            TextInput::make('article_detail.min_read'),
                                            TextInput::make('article_detail.more_from'),
                                            TextInput::make('article_detail.fallback_image'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Gallery, Stores & Search')
                                ->icon(Heroicon::OutlinedPhoto)
                                ->schema([
                                    Section::make('Gallery')
                                        ->schema([
                                            TextInput::make('gallery.seo.title'),
                                            Textarea::make('gallery.seo.description')->rows(2),
                                            TextInput::make('gallery.eyebrow'),
                                            TextInput::make('gallery.heading'),
                                            Textarea::make('gallery.intro')->rows(2),
                                            TextInput::make('gallery.intro_count'),
                                            TextInput::make('gallery.all'),
                                            TextInput::make('gallery.empty'),
                                        ])->columns(2),
                                    Section::make('Store locator')
                                        ->schema([
                                            TextInput::make('stores.seo.title'),
                                            Textarea::make('stores.seo.description')->rows(2),
                                            TextInput::make('stores.eyebrow'),
                                            TextInput::make('stores.heading'),
                                            TextInput::make('stores.location_label'),
                                            TextInput::make('stores.city_label'),
                                            TextInput::make('stores.all_states'),
                                            TextInput::make('stores.all_cities'),
                                            TextInput::make('stores.find_near_me'),
                                            TextInput::make('stores.store_count_one'),
                                            TextInput::make('stores.store_count_many'),
                                            TextInput::make('stores.directions'),
                                            TextInput::make('stores.call'),
                                            TextInput::make('stores.view_map'),
                                            TextInput::make('stores.empty'),
                                            TextInput::make('stores.clear'),
                                        ])->columns(2),
                                    Section::make('Search')
                                        ->schema([
                                            TextInput::make('search.seo.title'),
                                            Textarea::make('search.seo.description')->rows(2),
                                            TextInput::make('search.eyebrow'),
                                            TextInput::make('search.heading'),
                                            TextInput::make('search.placeholder'),
                                            TextInput::make('search.submit'),
                                            TextInput::make('search.min_chars'),
                                            TextInput::make('search.result_one'),
                                            TextInput::make('search.result_many'),
                                            TextInput::make('search.no_results'),
                                            TextInput::make('search.type_product'),
                                            TextInput::make('search.type_recipe'),
                                            TextInput::make('search.type_article'),
                                            TextInput::make('search.type_store'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Contact & Distributor')
                                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                                ->schema([
                                    Section::make('Contact page')
                                        ->schema([
                                            TextInput::make('contact.seo.title'),
                                            Textarea::make('contact.seo.description')->rows(2),
                                            TextInput::make('contact.heading'),
                                            Textarea::make('contact.intro')->rows(2),
                                            TextInput::make('contact.card_email'),
                                            TextInput::make('contact.card_phone'),
                                            TextInput::make('contact.card_address'),
                                            TextInput::make('contact.card_whatsapp'),
                                            TextInput::make('contact.phone_fallback'),
                                            TextInput::make('contact.address_fallback'),
                                            TextInput::make('contact.whatsapp_message'),
                                            TextInput::make('contact.map_url'),
                                            TextInput::make('contact.form_name'),
                                            TextInput::make('contact.form_email'),
                                            TextInput::make('contact.form_phone'),
                                            TextInput::make('contact.form_intent'),
                                            TextInput::make('contact.form_message'),
                                            TextInput::make('contact.form_submit'),
                                            TextInput::make('contact.form_sending'),
                                            TextInput::make('contact.success_fallback'),
                                            TextInput::make('contact.form_heading'),
                                            TextInput::make('contact.intent_label'),
                                            TextInput::make('contact.corporate_office'),
                                            TextInput::make('contact.whatsapp_cta'),
                                            TextInput::make('contact.call_cta'),
                                            TextInput::make('contact.directions_cta'),
                                            TextInput::make('contact.find_store_cta'),
                                            TextInput::make('contact.intents.order'),
                                            TextInput::make('contact.intents.retailer'),
                                            TextInput::make('contact.intents.product_info'),
                                            TextInput::make('contact.intents.complaint'),
                                            TextInput::make('contact.intents.distributor'),
                                            TextInput::make('contact.intents.partner'),
                                        ])->columns(2),
                                    Section::make('Distributor application')
                                        ->schema([
                                            TextInput::make('distributor.seo.title'),
                                            Textarea::make('distributor.seo.description')->rows(2),
                                            TextInput::make('distributor.heading'),
                                            Textarea::make('distributor.intro')->rows(2),
                                            TextInput::make('distributor.track_link'),
                                            TextInput::make('distributor.card_title'),
                                            TextInput::make('distributor.success_fallback'),
                                            TextInput::make('distributor.name'),
                                            TextInput::make('distributor.company'),
                                            TextInput::make('distributor.phone'),
                                            TextInput::make('distributor.email'),
                                            TextInput::make('distributor.state'),
                                            TextInput::make('distributor.lga'),
                                            TextInput::make('distributor.business_type'),
                                            TextInput::make('distributor.monthly_order'),
                                            TextInput::make('distributor.locations'),
                                            TextInput::make('distributor.submit'),
                                            TextInput::make('distributor.submitting'),
                                            TextInput::make('distributor.select_state'),
                                            TextInput::make('distributor.select_type'),
                                            TextInput::make('distributor.select_range'),
                                            TextInput::make('distributor.locations_placeholder'),
                                            TextInput::make('distributor.required_hint')->helperText('Shown beside required field labels.'),
                                            TextInput::make('distributor.business_types.retailer'),
                                            TextInput::make('distributor.business_types.distributor'),
                                            TextInput::make('distributor.business_types.wholesaler'),
                                            TextInput::make('distributor.business_types.online_seller'),
                                            TextInput::make('distributor.monthly_orders.1_5'),
                                            TextInput::make('distributor.monthly_orders.6_20'),
                                            TextInput::make('distributor.monthly_orders.20_plus'),
                                        ])->columns(2),
                                    Section::make('Distributor status')
                                        ->schema([
                                            TextInput::make('distributor_status.seo.title'),
                                            Textarea::make('distributor_status.seo.description')->rows(2),
                                            TextInput::make('distributor_status.heading'),
                                            Textarea::make('distributor_status.intro')->rows(2),
                                            TextInput::make('distributor_status.card_title'),
                                            TextInput::make('distributor_status.reference'),
                                            TextInput::make('distributor_status.submitted'),
                                            TextInput::make('distributor_status.reference_placeholder'),
                                            TextInput::make('distributor_status.check'),
                                            TextInput::make('distributor_status.checking'),
                                            TextInput::make('distributor_status.not_found'),
                                            TextInput::make('distributor_status.reference_label'),
                                            TextInput::make('distributor_status.status_new'),
                                            TextInput::make('distributor_status.status_approved'),
                                            TextInput::make('distributor_status.status_rejected'),
                                            TextInput::make('distributor_status.status_generic'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Account & Orders')
                                ->icon(Heroicon::OutlinedUser)
                                ->schema([
                                    Section::make('Account orders')
                                        ->schema([
                                            TextInput::make('account.seo.title'),
                                            Textarea::make('account.seo.description')->rows(2),
                                            TextInput::make('account.heading'),
                                            Textarea::make('account.intro')->rows(2),
                                            TextInput::make('account.empty'),
                                            TextInput::make('account.start_shopping'),
                                            TextInput::make('account.item_one'),
                                            TextInput::make('account.item_many'),
                                        ])->columns(2),
                                    Section::make('Order tracking')
                                        ->schema([
                                            TextInput::make('orders.seo.title'),
                                            Textarea::make('orders.seo.description')->rows(2),
                                            TextInput::make('orders.your_order'),
                                            TextInput::make('orders.tracking'),
                                            TextInput::make('orders.copy_order_number'),
                                            TextInput::make('orders.copied'),
                                            TextInput::make('orders.copy'),
                                            TextInput::make('orders.cancelled'),
                                            TextInput::make('orders.summary'),
                                            TextInput::make('orders.delivery_note'),
                                            TextInput::make('orders.delivery_note_placed'),
                                            TextInput::make('orders.whatsapp_cta'),
                                            TextInput::make('orders.whatsapp_cta_continue'),
                                            TextInput::make('orders.whatsapp_cta_message'),
                                            TextInput::make('orders.whatsapp_unavailable'),
                                            TextInput::make('orders.final_note'),
                                            TextInput::make('orders.carton'),
                                            TextInput::make('orders.quantity_times')->helperText('Use {quantity} and {size}.'),
                                        ])->columns(2),
                                ]),

                            Tab::make('Forms & Labels')
                                ->icon(Heroicon::OutlinedTag)
                                ->schema([
                                    Section::make('Order statuses')
                                        ->schema([
                                            TextInput::make('labels.order_statuses.pending'),
                                            TextInput::make('labels.order_statuses.confirmed'),
                                            TextInput::make('labels.order_statuses.ready'),
                                            TextInput::make('labels.order_statuses.delivered'),
                                            TextInput::make('labels.order_statuses.cancelled'),
                                        ])->columns(2),
                                    Section::make('Distributor statuses')
                                        ->schema([
                                            TextInput::make('labels.distributor_statuses.new'),
                                            TextInput::make('labels.distributor_statuses.in_progress'),
                                            TextInput::make('labels.distributor_statuses.approved'),
                                            TextInput::make('labels.distributor_statuses.rejected'),
                                            TextInput::make('labels.distributor_statuses.archived'),
                                        ])->columns(2),
                                    Section::make('Contact intents')
                                        ->schema([
                                            TextInput::make('labels.contact_intents.order'),
                                            TextInput::make('labels.contact_intents.retailer'),
                                            TextInput::make('labels.contact_intents.product_info'),
                                            TextInput::make('labels.contact_intents.complaint'),
                                            TextInput::make('labels.contact_intents.distributor'),
                                            TextInput::make('labels.contact_intents.partner'),
                                        ])->columns(2),
                                    Section::make('Distributor options')
                                        ->schema([
                                            TextInput::make('labels.business_types.retailer'),
                                            TextInput::make('labels.business_types.wholesaler'),
                                            TextInput::make('labels.business_types.supermarket'),
                                            TextInput::make('labels.business_types.distributor'),
                                            TextInput::make('labels.business_types.other'),
                                            TextInput::make('labels.monthly_orders.under_50'),
                                            TextInput::make('labels.monthly_orders.50_100'),
                                            TextInput::make('labels.monthly_orders.100_500'),
                                            TextInput::make('labels.monthly_orders.500_plus'),
                                        ])->columns(2),
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save website content')
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

        $aboutBody = $data['about_body'] ?? [];

        Setting::set('about.content', (string) json_encode([
            'who_we_are' => collect($aboutBody['who_we_are'] ?? [])
                ->pluck('paragraph')
                ->filter()
                ->values()
                ->all(),
            'vision' => $aboutBody['vision'] ?? '',
            'mission' => $aboutBody['mission'] ?? '',
            'values_intro' => $aboutBody['values_intro'] ?? '',
            'core_values' => collect($aboutBody['core_values'] ?? [])
                ->map(fn (array $value): array => [
                    'title' => $value['title'] ?? '',
                    'icon' => $value['icon'] ?? 'Users',
                    'description' => $value['description'] ?? '',
                ])
                ->values()
                ->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        foreach ([
            'global',
            'home',
            'about',
            'products',
            'product_detail',
            'shop',
            'recipes',
            'recipe_detail',
            'health',
            'article_detail',
            'gallery',
            'stores',
            'search',
            'contact',
            'distributor',
            'distributor_status',
            'account',
            'orders',
            'labels',
        ] as $key) {
            Setting::setContent($key, $data[$key] ?? []);
        }

        Notification::make()
            ->success()
            ->title('Website content saved')
            ->body('Your public site content has been updated.')
            ->send();
    }
}
