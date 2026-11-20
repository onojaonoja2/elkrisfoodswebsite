<?php

namespace App\Models;

use Database\Factories\GalleryItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryItem extends Model
{
    /** @use HasFactory<GalleryItemFactory> */
    use HasFactory;

    public const MEDIA_IMAGE = 'image';

    public const MEDIA_VIDEO = 'video';

    /**
     * Media hub categories (audit §20).
     *
     * @var list<string>
     */
    public const CATEGORIES = [
        'Products',
        'Recipes',
        'Customers',
        'Events',
        'Activations',
        'CSR',
        'Team',
        'Media',
    ];

    protected $fillable = [
        'title',
        'caption',
        'category',
        'media_type',
        'media_path',
        'sort_order',
        'is_published',
    ];

    protected $appends = [
        'media_url',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /** @scope */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isVideo(): bool
    {
        return $this->media_type === self::MEDIA_VIDEO;
    }

    public static function urlForPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'gallery/')) {
            return Storage::disk('s3')->url($path);
        }

        return $path;
    }

    protected function mediaUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => self::urlForPath($this->media_path));
    }
}
