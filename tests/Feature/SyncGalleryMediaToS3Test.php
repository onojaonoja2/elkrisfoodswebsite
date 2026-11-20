<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SyncGalleryMediaToS3Test extends TestCase
{
    use RefreshDatabase;

    private string $fixtureDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureDirectory = public_path('media/testing/sync-command');

        File::ensureDirectoryExists($this->fixtureDirectory);
        File::put($this->fixtureDirectory.'/clip.mp4', 'fake video');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('media/testing'));

        parent::tearDown();
    }

    public function test_it_uploads_local_media_and_updates_the_gallery_path(): void
    {
        Storage::fake('s3');

        $item = GalleryItem::factory()
            ->video('/media/testing/sync-command/clip.mp4')
            ->create();

        $this->artisan('media:sync-to-s3', ['--dry-run' => true])
            ->assertSuccessful();

        Storage::disk('s3')->assertMissing('gallery/website/testing/sync-command/clip.mp4');
        $this->assertSame('/media/testing/sync-command/clip.mp4', $item->fresh()->media_path);

        $this->artisan('media:sync-to-s3')
            ->assertSuccessful();

        Storage::disk('s3')->assertExists('gallery/website/testing/sync-command/clip.mp4');
        $this->assertSame(
            'gallery/website/testing/sync-command/clip.mp4',
            $item->fresh()->media_path,
        );
    }

    public function test_it_links_an_existing_s3_object_without_reuploading(): void
    {
        Storage::fake('s3');
        Storage::disk('s3')->put('gallery/website/testing/sync-command/clip.mp4', 'existing');

        $item = GalleryItem::factory()
            ->video('/media/testing/sync-command/clip.mp4')
            ->create();

        $this->artisan('media:sync-to-s3')
            ->assertSuccessful();

        $this->assertSame(
            'gallery/website/testing/sync-command/clip.mp4',
            $item->fresh()->media_path,
        );
        $this->assertSame(
            'existing',
            Storage::disk('s3')->get('gallery/website/testing/sync-command/clip.mp4'),
        );
    }
}
