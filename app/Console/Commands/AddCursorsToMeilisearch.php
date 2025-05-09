<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\cursor_tag_translation;

class AddCursorsToMeilisearch extends Command
{
    protected $signature = 'meilisearch:add-cursors';
    protected $description = 'Push all translated cursors and tags to Meilisearch for all languages';

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    public function handle()
    {
        $this->info("\n\u2728 Завантажуємо курсори з тегами у Meilisearch для всіх мов...\n");

        foreach ($this->languages as $lang) {
            $this->info("🌍 Мова: $lang");

            $tagged = cursor_tag_translation::with('cursor.category')
                ->where('lang', $lang)
                ->get();

            $documents = [];

            foreach ($tagged as $item) {
                if (!$item->cursor) continue;

                $documents[] = [
                    'id' => $item->cursor_id,
                    'name' => $item->cursor->name_en,
                    'tags' => $item->tags,
                    'lang' => $lang,
                    'cat' => $item->cursor->category->alt_name ?? null,
                    'cat_img' => $item->cursor->category->img ?? null,
                    'c_file' => $item->cursor->c_file,
                    'p_file' => $item->cursor->p_file,
                    'offsetX' => $item->cursor->offsetX,
                    'offsetY' => $item->cursor->offsetY,
                    'offsetX_p' => $item->cursor->offsetX_p,
                    'offsetY_p' => $item->cursor->offsetY_p,
                    'created_at' => $item->cursor->created_at->toDateTimeString(),
                ];
            }

            if (!empty($documents)) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer masterKey123',
                    'Content-Type' => 'application/json',
                ])->post("http://localhost:7700/indexes/cursors_{$lang}/documents", $documents);

                if ($response->failed()) {
                    $this->error("❌ Помилка для мови [$lang]: " . $response->body());
                    continue;
                }

                $this->info("✅ Завантажено " . count($documents) . " курсорів у індекс [$lang]\n");
            } else {
                $this->info("⚠️ Немає тегів для мови [$lang]\n");
            }
        }

        $this->info("\n🎉 Завершено додавання курсорів у Meilisearch для всіх мов.\n");
        return 0;
    }
}
