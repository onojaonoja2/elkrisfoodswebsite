<?php

namespace App\Filament\Resources\Recipes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Recipe')
                    ->schema([
                        TextInput::make('title')
                            ->required()
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
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('category')
                            ->required()
                            ->default('Swallow Recipes'),
                        FileUpload::make('image_path')
                            ->image()
                            ->directory('media/lifestyle')
                            ->nullable(),
                        TextInput::make('prep_minutes')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(1),
                        TextInput::make('servings')
                            ->required()
                            ->numeric()
                            ->default(4)
                            ->minValue(1),
                        Select::make('difficulty')
                            ->required()
                            ->default('Easy')
                            ->options([
                                'Easy' => 'Easy',
                                'Medium' => 'Medium',
                                'Hard' => 'Hard',
                            ]),
                        Textarea::make('summary')
                            ->rows(2)
                            ->columnSpanFull(),
                        TagsInput::make('ingredients')
                            ->placeholder('Add ingredient and press enter')
                            ->columnSpanFull(),
                        TagsInput::make('steps')
                            ->placeholder('Add step and press enter')
                            ->columnSpanFull(),
                        Textarea::make('tips')
                            ->label("Chef's tips")
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('meta_title')
                            ->maxLength(255),
                        FileUpload::make('og_image')
                            ->label('Social share image')
                            ->image()
                            ->directory('media/lifestyle')
                            ->nullable(),
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
