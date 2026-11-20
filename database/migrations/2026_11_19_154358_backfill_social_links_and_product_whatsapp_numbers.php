<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private array $productWhatsAppNumbers = [
        'elkris-oat-flour' => '07074868813',
        'elkris-oat-and-fibre-flour' => '07074892727',
        'elkris-instant-plantain-flour' => '09139584190',
        'elkris-premium-flours' => '07074711387',
    ];

    /**
     * @var array<string, string>
     */
    private array $settings = [
        'contact.whatsapp' => '07074711387',
        'social.facebook' => 'https://www.facebook.com/Elkrisfoods',
        'social.instagram' => 'https://www.instagram.com/elkrisfoods1',
        'social.tiktok' => 'https://www.tiktok.com/@elkris.foods',
        'social.youtube' => 'https://www.youtube.com/@elkrisfoods',
    ];

    public function up(): void
    {
        $plainFlour = DB::table('products')->where('slug', 'elkris-plain-flour')->first();

        if ($plainFlour) {
            DB::table('products')->where('id', $plainFlour->id)->update([
                'name' => 'Elkris Premium Flours',
                'slug' => 'elkris-premium-flours',
                'updated_at' => now(),
            ]);
        }

        foreach ($this->productWhatsAppNumbers as $slug => $whatsapp) {
            DB::table('products')
                ->where('slug', $slug)
                ->where(function ($query): void {
                    $query->whereNull('whatsapp')->orWhere('whatsapp', '');
                })
                ->update(['whatsapp' => $whatsapp, 'updated_at' => now()]);
        }

        foreach ($this->settings as $key => $value) {
            $setting = DB::table('settings')->where('key', $key)->first();

            if ($setting) {
                DB::table('settings')->where('id', $setting->id)->update([
                    'value' => $value,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Cache::forget("setting.{$key}");
        }

        Cache::forget('settings.public_map');
        Cache::forget('sitemap.entries');
    }

    public function down(): void
    {
        // The contact and social backfill is intentionally not reversed.
    }
};
