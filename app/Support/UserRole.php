<?php

namespace App\Support;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Author = 'author';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Editor => 'Editor',
            self::Author => 'Author',
            self::Customer => 'Customer',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role): array => [$role->value => $role->label()])
            ->all();
    }

    public function canAccessAdminPanel(): bool
    {
        return $this === self::Admin;
    }

    public function canAccessContentPanel(): bool
    {
        return in_array($this, [self::Admin, self::Editor, self::Author], true);
    }

    public function canWriteArticles(): bool
    {
        return $this->canAccessContentPanel();
    }

    public function canManageSiteContent(): bool
    {
        return in_array($this, [self::Admin, self::Editor], true);
    }
}
