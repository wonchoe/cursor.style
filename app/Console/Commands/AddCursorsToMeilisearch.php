<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\cursor;
use Illuminate\Support\Facades\Http;

class AddCursorsToMeilisearch extends Command
{
    protected $signature = 'meilisearch:add-cursors';
    protected $description = 'Push all cursors and tags to Meilisearch';

    public function handle()
    {
        $this->info("✨ Завантажуємо курсори у Meilisearch...");

        $cursors = cursor::with(['categories'])
            ->with(['tags' => function ($query) {
                $query->where('lang', 'en');
            }])
            ->get();

        $documents = [];

        foreach ($cursors as $cursor) {
            $tags = $cursor->tags->pluck('tags')->first();

            $documents[] = [
                'id' => $cursor->id,
                'name' => $cursor->name_en,
                'tags' => $tags,
                'cat' => $cursor->categories->alt_name ?? null,
                'cat_img' => $cursor->categories->img ?? null,
                'c_file' => $cursor->c_file,
                'p_file' => $cursor->p_file,
                'offsetX' => $cursor->offsetX,
                'offsetY' => $cursor->offsetY,
                'offsetX_p' => $cursor->offsetX_p,
                'offsetY_p' => $cursor->offsetY_p,
                'created_at' => $cursor->created_at->toDateTimeString(),
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer masterKey123',
            'Content-Type' => 'application/json',
        ])->post('http://localhost:7700/indexes/cursors/documents', $documents);

        if ($response->failed()) {
            $this->error("❌ Не вдалося додати документи у Meilisearch: " . $response->body());
            return 1;
        }

        $this->info("🎉 Успішно додано: " . count($documents) . " курсорів у Meilisearch.");
        return 0;
    }
}
