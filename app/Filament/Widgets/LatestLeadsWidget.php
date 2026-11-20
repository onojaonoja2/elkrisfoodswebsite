<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactLeads\ContactLeadResource;
use App\Models\ContactLead;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeadsWidget extends TableWidget
{
    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest contact messages')
            ->query(ContactLead::query()->latest())
            ->columns([
                TextColumn::make('created_at')->since()->label('Received'),
                TextColumn::make('name')->weight('bold')->limit(20),
                TextColumn::make('intent')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (ContactLead $record): string => ContactLeadResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
