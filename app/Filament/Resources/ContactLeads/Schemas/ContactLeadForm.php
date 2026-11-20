<?php

namespace App\Filament\Resources\ContactLeads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('intent')
                    ->options([
                        'order' => 'Order',
                        'retailer' => 'Retailer',
                        'product_info' => 'Product info',
                        'complaint' => 'Complaint',
                        'distributor' => 'Distributor',
                        'partner' => 'Partner',
                    ])
                    ->required()
                    ->disabled(),
                TextInput::make('name')
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->disabled(),
                Textarea::make('message')
                    ->required()
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'resolved' => 'Resolved',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('new'),
            ]);
    }
}
