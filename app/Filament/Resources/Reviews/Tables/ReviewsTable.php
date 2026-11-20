<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Reviewer')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (int $state): string => $state.' ★'),
                TextColumn::make('body')
                    ->label('Review')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Review::STATUS_APPROVED => 'success',
                        Review::STATUS_PENDING => 'warning',
                        Review::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Review::STATUS_PENDING => 'Pending',
                        Review::STATUS_APPROVED => 'Approved',
                        Review::STATUS_REJECTED => 'Rejected',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Review $record): bool => $record->status !== Review::STATUS_APPROVED)
                    ->action(fn (Review $record) => $record->update(['status' => Review::STATUS_APPROVED])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(function (Collection $records): void {
                            $records->each(fn (Review $review) => $review->update(['status' => Review::STATUS_APPROVED]));
                        }),
                    BulkAction::make('reject')
                        ->label('Reject')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->action(function (Collection $records): void {
                            $records->each(fn (Review $review) => $review->update(['status' => Review::STATUS_REJECTED]));
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
