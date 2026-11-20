<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;

class VideoEmbedBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'video_embed';
    }

    public static function getLabel(): string
    {
        return 'Video embed';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Choose a video upload or paste a YouTube or Vimeo link.')
            ->schema([
                Select::make('source')
                    ->label('Video source')
                    ->options([
                        'upload' => 'Upload a video',
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                    ])
                    ->default('upload')
                    ->required()
                    ->live(),
                FileUpload::make('path')
                    ->label('Video file')
                    ->disk('s3')
                    ->directory('articles/body/videos')
                    ->visibility('public')
                    ->acceptedFileTypes([
                        'video/mp4',
                        'video/webm',
                    ])
                    ->maxSize(102400)
                    ->fetchFileInformation(false)
                    ->required(fn (Get $get): bool => $get('source') === 'upload')
                    ->visible(fn (Get $get): bool => $get('source') === 'upload')
                    ->helperText('MP4 or WebM, up to 100 MB. Use YouTube or Vimeo for larger videos.')
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('YouTube or Vimeo URL')
                    ->url()
                    ->required(fn (Get $get): bool => $get('source') !== 'upload')
                    ->visible(fn (Get $get): bool => $get('source') !== 'upload')
                    ->rule(static function (string $attribute, mixed $value, Closure $fail): void {
                        if (blank($value) || static::embedUrl((string) $value) !== null) {
                            return;
                        }

                        $fail('Enter a valid YouTube or Vimeo video URL.');
                    })
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(200)
                    ->columnSpanFull(),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.video-embed.preview', [
            'content' => static::toHtml($config, []),
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        $caption = trim((string) ($config['caption'] ?? ''));
        $title = $caption !== '' ? $caption : 'Video';
        $source = (string) ($config['source'] ?? 'upload');

        if ($source === 'upload') {
            $path = trim((string) ($config['path'] ?? ''));

            if (! static::isValidVideoPath($path)) {
                return '';
            }

            return view('filament.forms.components.rich-editor.rich-content-custom-blocks.video-embed.index', [
                'caption' => $caption,
                'embedUrl' => null,
                'title' => $title,
                'videoUrl' => Storage::disk('s3')->url($path),
            ])->render();
        }

        $embedUrl = static::embedUrl((string) ($config['url'] ?? ''));

        if ($embedUrl === null) {
            return '';
        }

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.video-embed.index', [
            'caption' => $caption,
            'embedUrl' => $embedUrl,
            'title' => $title,
            'videoUrl' => null,
        ])->render();
    }

    public static function embedUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);

        if (! is_array($parts) || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $videoId = null;

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            if ($path === 'watch') {
                parse_str((string) ($parts['query'] ?? ''), $query);
                $videoId = $query['v'] ?? null;
            } elseif (in_array($segments[0] ?? null, ['embed', 'live', 'shorts'], true)) {
                $videoId = $segments[1] ?? null;
            }
        } elseif (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = $segments[0] ?? null;
        }

        if (is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1) {
            return "https://www.youtube-nocookie.com/embed/{$videoId}";
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)) {
            if (($segments[0] ?? null) === 'video') {
                $videoId = $segments[1] ?? null;
                parse_str((string) ($parts['query'] ?? ''), $query);
                $hash = $query['h'] ?? null;
            } else {
                $videoId = $segments[0] ?? null;
                $hash = $segments[1] ?? null;
            }

            if (is_string($videoId) && ctype_digit($videoId)) {
                $embedUrl = "https://player.vimeo.com/video/{$videoId}";

                if (is_string($hash) && preg_match('/^[A-Za-z0-9]+$/', $hash) === 1) {
                    $embedUrl .= "?h={$hash}";
                }

                return $embedUrl;
            }
        }

        return null;
    }

    protected static function isValidVideoPath(string $path): bool
    {
        return str_starts_with($path, 'articles/body/videos/')
            && ! str_contains($path, '..')
            && ! str_contains($path, '\\');
    }
}
