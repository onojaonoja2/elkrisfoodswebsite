<?php

namespace App\Models;

use Database\Factories\MealTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MealType extends Model
{
    /** @use HasFactory<MealTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'usage_copy',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_meal_type');
    }
}
