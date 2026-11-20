<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FAQ')
                    ->schema([
                        TextInput::make('question')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('category')
                            ->options([
                                'Products' => 'Products',
                                'Stores' => 'Stores',
                                'Orders' => 'Orders',
                                'General' => 'General',
                            ])
                            ->default('General')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
