<?php

namespace App\Filament\Resources\GalleryItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GalleryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category')
                    ->badge()
                    ->color('info'),
                TextColumn::make('media_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'video' ? 'warning' : 'success'),
                TextColumn::make('media_path')
                    ->label('File')
                    ->limit(45)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()),
                TextColumn::make('sort_order')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->multiple(),
                SelectFilter::make('media_type')
                    ->label('Type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ]),
                TernaryFilter::make('is_published'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
