<?php

namespace App\Models;

use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'phone',
        'opening_hours',
        'latitude',
        'longitude',
        'google_maps_url',
        'products_available',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'products_available' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function scopeFilter(Builder $query, ?string $state, ?string $city): Builder
    {
        return $query
            ->when($state, fn (Builder $q) => $q->where('state', $state))
            ->when($city, fn (Builder $q) => $q->where('city', $city));
    }

    /** @return list<string> */
    public static function states(): array
    {
        return static::query()
            ->where('is_published', true)
            ->orderBy('state')
            ->distinct()
            ->pluck('state')
            ->all();
    }

    public function directionsUrl(): string
    {
        if ($this->google_maps_url) {
            return $this->google_maps_url;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
        }

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode(trim("{$this->name} {$this->address} {$this->city} {$this->state}"));
    }
}
