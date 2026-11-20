<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'tagline',
        'description',
        'benefits',
        'best_with',
        'nutrition_notes',
        'whatsapp',
        'meta_title',
        'meta_description',
        'og_image',
        'hero_image',
        'gallery',
        'sort_order',
        'is_published',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'best_with' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected function heroImage(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => Media::url($value),
            set: fn (?string $value): ?string => Media::path($value),
        );
    }

    protected function ogImage(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => Media::url($value),
            set: fn (?string $value): ?string => Media::path($value),
        );
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => Media::urls(json_decode($value, true) ?? []) ?? [],
            set: fn ($value): string => json_encode(Media::paths(is_array($value) ? $value : []) ?? []),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function mealTypes(): BelongsToMany
    {
        return $this->belongsToMany(MealType::class, 'product_meal_type');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function lowestPrice(): ?int
    {
        return $this->variants->where('is_available', true)->min('price');
    }
}
