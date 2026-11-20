<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product details')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('tagline')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        TagsInput::make('benefits')
                            ->placeholder('Add a benefit and press enter')
                            ->columnSpanFull(),
                        TagsInput::make('best_with')
                            ->placeholder('e.g. Egusi, Okro, Ogbono')
                            ->columnSpanFull(),
                        Textarea::make('nutrition_notes')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp number')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Leave blank to use the site-wide WhatsApp number.'),
                    ])->columns(2),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->image()
                            ->directory('media/products')
                            ->nullable(),
                        FileUpload::make('gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('media/products')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Meal occasions')
                    ->schema([
                        Select::make('mealTypes')
                            ->label('Meal types')
                            ->relationship('mealTypes', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('slug')->required(),
                            ]),
                    ])->columns(2),

                Section::make('Variants')
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                TextInput::make('size_label')
                                    ->required()
                                    ->maxLength(50),
                                Select::make('pack_type')
                                    ->options([
                                        'single' => 'Single pack',
                                        'carton' => 'Carton',
                                    ])
                                    ->default('single')
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Price (kobo or ₦ whole — as stored)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('sku')
                                    ->maxLength(50),
                                Toggle::make('is_available')
                                    ->default(true)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => trim(($state['size_label'] ?? '').' '.$state['pack_type']) ?: null)
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255),
                        FileUpload::make('og_image')
                            ->label('Social share image')
                            ->image()
                            ->directory('media/products')
                            ->nullable(),
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Publishing')
                    ->schema([
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                        Toggle::make('is_featured')
                            ->default(false)
                            ->required(),
                    ])->columns(3),
            ]);
    }
}
