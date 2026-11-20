<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Support\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->options(UserRole::options())
                    ->required()
                    ->default(UserRole::Author->value)
                    ->disabled(fn (?User $record): bool => $record !== null && $record->is(auth()->user()))
                    ->helperText(fn (?User $record): ?string => $record !== null && $record->is(auth()->user())
                        ? 'Your own role cannot be changed here.'
                        : null),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->rules(fn (): array => array_values(array_filter([Password::default()])))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Leave blank when editing to keep the current password.'),
            ]);
    }
}
