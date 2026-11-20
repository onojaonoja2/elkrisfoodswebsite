<?php

namespace App\Models;

use App\Support\Media;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'quote',
        'photo_path',
        'video_url',
        'is_featured',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected function photoPath(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => Media::url($value),
            set: fn (?string $value): ?string => Media::path($value),
        );
    }
}
