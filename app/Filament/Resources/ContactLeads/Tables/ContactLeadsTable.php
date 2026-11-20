<?php

namespace App\Filament\Resources\ContactLeads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('intent')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'complaint' => 'danger',
                        'order' => 'success',
                        'distributor', 'partner', 'retailer' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('message')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('intent')
                    ->options([
                        'order' => 'Order',
                        'retailer' => 'Retailer',
                        'product_info' => 'Product info',
                        'complaint' => 'Complaint',
                        'distributor' => 'Distributor',
                        'partner' => 'Partner',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'resolved' => 'Resolved',
                        'archived' => 'Archived',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
