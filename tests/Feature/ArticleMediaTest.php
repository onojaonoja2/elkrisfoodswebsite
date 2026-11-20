<?php

namespace Tests\Feature;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\VideoEmbedBlock;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_youtube_urls_render_a_privacy_enhanced_embed_with_an_escaped_caption(): void
    {
        $html = VideoEmbedBlock::toHtml([
            'source' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'caption' => 'A <script>alert("x")</script> clip',
        ], []);

        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_vimeo_urls_render_an_embed(): void
    {
        $html = VideoEmbedBlock::toHtml([
            'source' => 'vimeo',
            'url' => 'https://vimeo.com/76979871/abc123',
        ], []);

        $this->assertStringContainsString('https://player.vimeo.com/video/76979871?h=abc123', $html);
    }

    public function test_non_youtube_or_vimeo_urls_do_not_render_an_embed(): void
    {
        $html = VideoEmbedBlock::toHtml([
            'source' => 'youtube',
            'url' => 'https://example.com/watch?v=dQw4w9WgXcQ',
        ], []);

        $this->assertSame('', $html);
    }

    public function test_uploaded_videos_render_from_public_s3_storage(): void
    {
        Storage::fake('s3', ['url' => 'https://cdn.example.test']);

        $html = VideoEmbedBlock::toHtml([
            'source' => 'upload',
            'path' => 'articles/body/videos/demo.mp4',
            'caption' => 'A demo clip',
        ], []);

        $this->assertStringContainsString('https://cdn.example.test/articles/body/videos/demo.mp4', $html);
        $this->assertStringContainsString('<video', $html);
        $this->assertStringContainsString('A demo clip', $html);
    }

    public function test_video_uploads_outside_the_article_media_directory_are_rejected(): void
    {
        Storage::fake('s3');

        foreach ([
            '../secret.mp4',
            'articles/body/videos/../secret.mp4',
            'gallery/website/demo.mp4',
            'articles/body/videos\\demo.mp4',
        ] as $path) {
            $this->assertSame('', VideoEmbedBlock::toHtml([
                'source' => 'upload',
                'path' => $path,
            ], []));
        }
    }

    public function test_article_body_html_renders_registered_custom_video_blocks(): void
    {
        $config = json_encode([
            'source' => 'youtube',
            'url' => 'https://youtu.be/dQw4w9WgXcQ',
            'caption' => 'A custom block clip',
        ], JSON_THROW_ON_ERROR);

        $article = Article::factory()->create([
            'body' => '<div data-type="customBlock" data-id="video_embed" data-config=\''.$config.'\'></div>',
        ]);

        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $article->body_html);
    }

    public function test_article_body_html_renders_uploaded_video_blocks(): void
    {
        Storage::fake('s3', ['url' => 'https://cdn.example.test']);

        $config = json_encode([
            'source' => 'upload',
            'path' => 'articles/body/videos/demo.mp4',
        ], JSON_THROW_ON_ERROR);

        $article = Article::factory()->create([
            'body' => '<div data-type="customBlock" data-id="video_embed" data-config=\''.$config.'\'></div>',
        ]);

        $this->assertStringContainsString('<video', $article->body_html);
        $this->assertStringContainsString('https://cdn.example.test/articles/body/videos/demo.mp4', $article->body_html);
    }

    public function test_article_body_html_is_sanitized(): void
    {
        $article = Article::factory()->create([
            'body' => '<p>Safe content</p><script>alert(1)</script>',
        ]);

        $this->assertStringContainsString('<p>Safe content</p>', $article->body_html);
        $this->assertStringNotContainsString('<script', $article->body_html);
    }

    public function test_article_body_html_removes_iframes_from_untrusted_hosts(): void
    {
        $article = Article::factory()->create([
            'body' => '<p>Safe content</p><iframe src="https://example.com/embed"></iframe>',
        ]);

        $this->assertStringContainsString('<p>Safe content</p>', $article->body_html);
        $this->assertStringNotContainsString('<iframe', $article->body_html);
    }
}
