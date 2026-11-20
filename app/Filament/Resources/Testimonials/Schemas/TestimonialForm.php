<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Testimonial')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('location'),
                        Textarea::make('quote')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('photo_path')
                            ->image()
                            ->directory('media/testimonials')
                            ->nullable(),
                        TextInput::make('video_url')
                            ->url()
                            ->placeholder('https://youtube.com/...')
                            ->columnSpanFull(),
                        Toggle::make('is_featured')
                            ->default(false)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
