<?php

namespace App\Filament\Resources\DistributorLeads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DistributorLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->label('Reference')
                    ->disabled(),
                TextInput::make('name')
                    ->required()
                    ->disabled(),
                TextInput::make('company')
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->disabled(),
                TextInput::make('state')
                    ->required()
                    ->disabled(),
                TextInput::make('lga')
                    ->disabled(),
                TextInput::make('business_type')
                    ->disabled(),
                TextInput::make('monthly_order')
                    ->disabled(),
                Textarea::make('locations')
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('new'),
            ]);
    }
}
