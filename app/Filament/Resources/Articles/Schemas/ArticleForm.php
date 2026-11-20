<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('category')
                            ->required()
                            ->options(
                                collect(Article::CATEGORIES)
                                    ->mapWithKeys(fn (string $category) => [$category => $category])
                                    ->all(),
                            )
                            ->default(Article::CATEGORIES[0])
                            ->searchable(),
                        TextInput::make('author')
                            ->maxLength(255)
                            ->helperText('Public byline. Defaults to your name when left blank.'),
                        TextInput::make('reading_minutes')
                            ->label('Reading time (minutes)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(4),
                        FileUpload::make('cover_path')
                            ->image()
                            ->directory('media/lifestyle')
                            ->nullable(),
                        Textarea::make('excerpt')
                            ->rows(2)
                            ->helperText('Short summary shown on the Health hub and in search results.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->required()
                            ->fileAttachmentsDisk('s3')
                            ->fileAttachmentsDirectory('articles/body/images')
                            ->fileAttachmentsVisibility('public')
                            ->preventFileAttachmentPathTampering()
                            ->helperText('Use the toolbar to insert images and video. Uploaded media is stored on S3.')
                            ->columnSpanFull(),
                    ])->columns(2),

                TextEntry::make('health_claims_guidance')
                    ->label('Health claims guidance (audit §17)')
                    ->state('Use qualified language only. Allowed: "A fibre-rich option for people making more conscious food choices." and "Contains beta-glucan, a soluble fibre associated with heart-health benefits as part of a balanced diet." Never write treatment claims such as "controls diabetes" or "lowers blood sugar". Taglines ("Trusted for Healthy Foods", "Blood Sugar Friendlier Alternative Swallow") belong in marketing surfaces only.')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->color('warning')
                    ->columnSpanFull(),

                Section::make('Publishing')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('Overrides the article title in search results.'),
                        FileUpload::make('og_image')
                            ->label('Social share image')
                            ->image()
                            ->directory('media/lifestyle')
                            ->nullable(),
                        TextInput::make('meta_description')
                            ->maxLength(255)
                            ->helperText('Shown in search results and social shares.')
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Turn off to save this post as a draft. Drafts stay hidden from the Health Hub.')
                            ->default(true)
                            ->required(),
                        DateTimePicker::make('published_at'),
                        TextEntry::make('views')
                            ->label('Total views')
                            ->icon(Heroicon::OutlinedEye)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                    ])->columns(3),
            ]);
    }
}
