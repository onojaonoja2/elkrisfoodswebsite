<?php

namespace App\Console\Commands;

use App\Models\GalleryItem;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

#[Signature('media:sync-to-s3 {--dry-run} {--force}')]
#[Description('Upload local gallery media to the S3 gallery/website prefix')]
class SyncGalleryMediaToS3 extends Command
{
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $disk = Storage::disk('s3');

        $counts = [
            'uploaded' => 0,
            'linked' => 0,
            'skipped' => 0,
            'missing' => 0,
            'failed' => 0,
        ];

        GalleryItem::query()
            ->orderBy('id')
            ->each(function (GalleryItem $item) use ($disk, $dryRun, $force, &$counts): void {
                $path = $item->media_path;

                if (blank($path) || Str::startsWith($path, ['http://', 'https://'])) {
                    $counts['skipped']++;

                    return;
                }

                if (Str::startsWith($path, 'gallery/')) {
                    if ($disk->exists($path)) {
                        $counts['skipped']++;
                    } else {
                        $counts['missing']++;
                        $this->warn("Missing S3 object for [{$item->title}]: {$path}");
                    }

                    return;
                }

                $relative = Str::of($path)
                    ->replaceStart('/media/', '')
                    ->replaceStart('media/', '')
                    ->ltrim('/')
                    ->toString();

                $source = public_path('media/'.$relative);
                $target = 'gallery/website/'.$relative;
                $mediaRoot = realpath(public_path('media'));
                $realSource = realpath($source);

                if (
                    $mediaRoot === false
                    || $realSource === false
                    || ! Str::startsWith($realSource, $mediaRoot.DIRECTORY_SEPARATOR)
                    || ! File::isFile($realSource)
                ) {
                    $counts['missing']++;
                    $this->warn("Missing local file for [{$item->title}]: {$path}");

                    return;
                }

                if (! $force && $disk->exists($target)) {
                    if (! $dryRun) {
                        $item->forceFill(['media_path' => $target])->save();
                    }

                    $counts['linked']++;

                    return;
                }

                if ($dryRun) {
                    $counts['uploaded']++;
                    $this->line("Would upload {$path} -> {$target}");

                    return;
                }

                $stream = fopen($realSource, 'rb');

                if ($stream === false) {
                    $counts['failed']++;
                    $this->error("Unable to open [{$path}] for reading.");

                    return;
                }

                try {
                    $mimeType = File::mimeType($realSource) ?: 'application/octet-stream';

                    if (! $disk->put($target, $stream, ['ContentType' => $mimeType])) {
                        throw new \RuntimeException('The storage disk rejected the upload.');
                    }
                } catch (Throwable $exception) {
                    $counts['failed']++;
                    $this->error("Failed to upload [{$path}]: {$exception->getMessage()}");

                    return;
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }

                $item->forceFill(['media_path' => $target])->save();
                $counts['uploaded']++;
            });

        $this->newLine();
        $this->table(
            ['Uploaded', 'Linked', 'Skipped', 'Missing', 'Failed'],
            [[
                $counts['uploaded'],
                $counts['linked'],
                $counts['skipped'],
                $counts['missing'],
                $counts['failed'],
            ]],
        );

        return $counts['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
