<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    /** @use HasFactory<RecipeFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'slug',
        'category',
        'image_path',
        'prep_minutes',
        'servings',
        'difficulty',
        'ingredients',
        'steps',
        'tips',
        'summary',
        'meta_title',
        'meta_description',
        'og_image',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'steps' => 'array',
            'prep_minutes' => 'integer',
            'servings' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected function imagePath(): Attribute
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
