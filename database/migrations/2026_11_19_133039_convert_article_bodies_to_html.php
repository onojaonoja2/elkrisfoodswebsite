<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('articles')
            ->select(['id', 'body'])
            ->orderBy('id')
            ->chunkById(100, function ($articles): void {
                foreach ($articles as $article) {
                    $body = trim((string) $article->body);

                    if ($body === '' || str_contains($body, '<')) {
                        continue;
                    }

                    $paragraphs = preg_split('/\R{2,}/', $body) ?: [];

                    $html = collect($paragraphs)
                        ->map(fn (string $paragraph): string => trim($paragraph))
                        ->filter()
                        ->map(fn (string $paragraph): string => '<p>'.nl2br(e($paragraph), false).'</p>')
                        ->implode('');

                    DB::table('articles')
                        ->where('id', $article->id)
                        ->update(['body' => $html]);
                }
            });
    }

    public function down(): void {}
};
