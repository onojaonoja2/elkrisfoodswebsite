<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            return static::query()->where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
        Cache::forget('settings.public_map');
    }

    /**
     * @return array<string, mixed>
     */
    public static function content(string $key): array
    {
        $defaults = config("site_content.{$key}", []);
        $stored = static::get("content.{$key}");

        if (blank($stored)) {
            return $defaults;
        }

        $decoded = json_decode($stored, true);

        if (! is_array($decoded)) {
            return $defaults;
        }

        return static::mergeContent($defaults, $decoded);
    }

    /**
     * @param  array<string, mixed>  $content
     */
    public static function setContent(string $key, array $content): void
    {
        static::set("content.{$key}", (string) json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Cache::forget('settings.public_map');
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function mergeContent(array $defaults, array $stored): array
    {
        foreach ($defaults as $key => $defaultValue) {
            if (! array_key_exists($key, $stored)) {
                $stored[$key] = $defaultValue;

                continue;
            }

            if (is_array($defaultValue) && is_array($stored[$key])) {
                if (array_is_list($defaultValue) || array_is_list($stored[$key])) {
                    continue;
                }

                $stored[$key] = static::mergeContent($defaultValue, $stored[$key]);
            }
        }

        return $stored;
    }

    /**
     * @return array{who_we_are: list<string>, vision: string, mission: string, values_intro: string, core_values: list<array{title: string, icon: string, description: string}>}
     */
    public static function aboutContent(): array
    {
        $defaults = config('about.content', []);
        $stored = static::get('about.content');

        if (blank($stored)) {
            return $defaults;
        }

        $decoded = json_decode($stored, true);

        if (! is_array($decoded)) {
            return $defaults;
        }

        return array_replace($defaults, $decoded);
    }

    /**
     * Key/value map of the public site settings shared with the frontend.
     *
     * @return array<string, string|null>
     */
    public static function publicMap(): array
    {
        return Cache::remember('settings.public_map', 3600, function (): array {
            $keys = [
                'brand.primary_tagline',
                'brand.positioning_tagline',
                'brand.positioning_line',
                'contact.email',
                'contact.phone',
                'contact.address',
                'contact.whatsapp',
                'delivery.info',
                'social.facebook',
                'social.instagram',
                'social.tiktok',
                'social.youtube',
                'social.x',
                'analytics.ga4_id',
                'analytics.meta_pixel_id',
            ];

            $settings = static::query()->whereIn('key', $keys)->pluck('value', 'key');

            $map = [];
            foreach ($keys as $key) {
                $map[$key] = $settings[$key] ?? null;
            }

            return $map;
        });
    }
}
