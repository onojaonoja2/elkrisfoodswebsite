<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Store')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('address')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('city')
                            ->required(),
                        TextInput::make('state')
                            ->required(),
                        TextInput::make('phone')
                            ->tel(),
                        TextInput::make('opening_hours'),
                        TextInput::make('google_maps_url')
                            ->url(),
                        Textarea::make('products_available')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Map coordinates')
                    ->schema([
                        TextInput::make('latitude')
                            ->numeric(),
                        TextInput::make('longitude')
                            ->numeric(),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                    ])->columns(3),
            ]);
    }
}
