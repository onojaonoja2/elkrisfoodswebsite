<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscriber')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email address')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->maxLength(255),
                        Select::make('source')
                            ->required()
                            ->options([
                                'footer' => 'Footer signup',
                                'home' => 'Homepage signup',
                                'manual' => 'Added manually',
                            ])
                            ->default('footer'),
                        DateTimePicker::make('subscribed_at'),
                        DateTimePicker::make('unsubscribed_at')
                            ->helperText('Leave empty for active subscribers.'),
                    ])->columns(2),
            ]);
    }
}
