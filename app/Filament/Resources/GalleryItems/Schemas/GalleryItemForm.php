<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use App\Models\GalleryItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Media item')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->required()
                            ->options(
                                collect(GalleryItem::CATEGORIES)
                                    ->mapWithKeys(fn (string $category) => [$category => $category])
                                    ->all(),
                            )
                            ->default(GalleryItem::CATEGORIES[0]),
                        Select::make('media_type')
                            ->label('Type')
                            ->required()
                            ->options([
                                GalleryItem::MEDIA_IMAGE => 'Image',
                                GalleryItem::MEDIA_VIDEO => 'Video',
                            ])
                            ->default(GalleryItem::MEDIA_IMAGE),
                        FileUpload::make('media_path')
                            ->label('Media file')
                            ->disk('s3')
                            ->directory('gallery/website')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/webm',
                            ])
                            ->maxSize(512000)
                            ->fetchFileInformation(false)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Upload an image or video. Files are stored in S3 under gallery/website.'),
                        Textarea::make('caption')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Display')
                    ->schema([
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
