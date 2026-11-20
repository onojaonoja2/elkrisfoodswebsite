<?php

namespace App\Support;

use Illuminate\Support\Str;

class Media
{
    public static function path(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//', 'data:'])) {
            return $value;
        }

        return ltrim($value, '/');
    }

    public static function url(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//', 'data:'])) {
            return $value;
        }

        $path = ltrim($value, '/');

        if (Str::startsWith($path, 'media/')) {
            return '/'.$path;
        }

        return '/media/'.$path;
    }

    /**
     * @param  array<int, string>|null  $values
     * @return array<int, string>|null
     */
    public static function paths(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return array_values(array_map(
            fn (?string $value): ?string => self::path($value),
            $values,
        ));
    }

    /**
     * @param  array<int, string>|null  $values
     * @return array<int, string>|null
     */
    public static function urls(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return array_values(array_map(
            fn (?string $value): ?string => self::url($value),
            $values,
        ));
    }
}
