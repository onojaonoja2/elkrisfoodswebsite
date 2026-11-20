<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'description',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
