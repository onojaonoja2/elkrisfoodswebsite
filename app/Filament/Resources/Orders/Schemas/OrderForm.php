<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Order reference')
                    ->required()
                    ->disabled(),
                Select::make('status')
                    ->label('Status')
                    ->options(Order::STATUS_LABELS)
                    ->required()
                    ->default(Order::STATUS_PENDING),
                TextInput::make('name')
                    ->label('Customer')
                    ->required()
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->disabled(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Textarea::make('notes')
                    ->label('Delivery notes')
                    ->disabled()
                    ->columnSpanFull(),
                DateTimePicker::make('whatsapp_sent_at')
                    ->label('WhatsApp confirmation sent')
                    ->helperText('Set manually when the customer has been contacted on WhatsApp.'),
            ]);
    }
}
