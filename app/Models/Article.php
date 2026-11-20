<?php

namespace App\Models;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\VideoEmbedBlock;
use App\Support\Media;
use Database\Factories\ArticleFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model implements HasRichContent
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use InteractsWithRichContent;

    /**
     * Health Hub content categories.
     *
     * @var list<string>
     */
    public const CATEGORIES = [
        'Nutrition',
        'Blood Sugar Friendly Living',
        'Product Education',
        'Healthy Habits',
        'Brand News',
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'cover_path',
        'author',
        'author_id',
        'reading_minutes',
        'meta_title',
        'meta_description',
        'og_image',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'reading_minutes' => 'integer',
            'views' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected function setUpRichContent(): void
    {
        $this->registerRichContent('body')
            ->fileAttachmentsDisk('s3')
            ->fileAttachmentsVisibility('public')
            ->customBlocks([
                VideoEmbedBlock::class,
            ]);
    }

    protected function bodyHtml(): Attribute
    {
        return Attribute::make(
            get: fn (): string => (string) $this->renderRichContent('body'),
        );
    }

    protected function coverPath(): Attribute
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

    /** @scope */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * First paragraphs rendered as a plain-text search snippet.
     */
    public function excerptOrSummary(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return mb_substr(trim(html_entity_decode(strip_tags((string) $this->body), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 0, 220);
    }
}
