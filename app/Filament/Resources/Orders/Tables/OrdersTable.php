<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('number')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('items.count')
                    ->label('Items')
                    ->counts('items'),
                TextColumn::make('subtotal')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => '₦'.number_format((float) $state))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PENDING => 'warning',
                        Order::STATUS_CONFIRMED => 'info',
                        Order::STATUS_READY => 'primary',
                        Order::STATUS_DELIVERED => 'success',
                        Order::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Order::STATUS_LABELS),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Order $record): string => self::customerWhatsAppUrl($record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('changeStatus')
                        ->label('Change status')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->form([
                            Select::make('status')
                                ->label('New status')
                                ->options(Order::STATUS_LABELS)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Order $order) => $order->update(['status' => $data['status']]));
                        }),
                ]),
            ]);
    }

    private static function customerWhatsAppUrl(Order $record): string
    {
        $digits = preg_replace('/[^\d]/', '', $record->phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '234'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '234')) {
            $digits = '234'.$digits;
        }

        $message = "Hello {$record->name}, this is Elkris Foods about your order {$record->number}.";

        return 'https://wa.me/'.$digits.'?text='.urlencode($message);
    }
}
