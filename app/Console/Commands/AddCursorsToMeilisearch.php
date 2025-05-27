<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CursorTagTranslation;

class AddCursorsToMeilisearch extends Command
{
    protected $signature = 'custom:meilisearchAddCursors {--force : Drop and recreate each index before pushing data}';
    protected $description = 'Push all translated cursors and tags to Meilisearch for all languages';

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    public function handle()
    {
        $force = $this->option('force');

        $this->info("\n✨ Завантажуємо курсори з тегами у Meilisearch для всіх мов...");
        if ($force) {
            $this->warn("⚠️  Увімкнено режим --force: індекси будуть повністю очищені перед додаванням\n");
        }

        foreach ($this->languages as $lang) {
            app()->setLocale($lang); // 👈 ДОДАЙ ЦЕ            
            $this->info("🌍 Мова: $lang");

            $tagged = CursorTagTranslation::with('cursor.collection')
                ->where(function ($q) use ($lang) {
                    $q->where('lang', $lang)
                    ->orWhere(function ($q2) use ($lang) {
                        $q2->where('lang', 'en')->whereNotIn('cursor_id', function ($q3) use ($lang) {
                            $q3->select('cursor_id')
                                ->from('cursor_tag_translations')
                                ->where('lang', $lang);
                        });
                    });
                })
                ->get();

            $documents = [];

            $this->info("🌍 Update1");

            foreach ($tagged as $item) {
                if (!$item->cursor) continue;

                $name = trans("cursors.c_{$item->cursor_id}", [], $lang);
                if ($name === "cursors.c_{$item->cursor_id}") {
                    $name = $item->cursor->name_en;
                }                          

                $catAlt = optional($item->cursor->collection)->alt_name;
                $catKey = "collections.{$catAlt}";
                $catTranslated = trans($catKey, [], $lang);
                
                if ($catTranslated === $catKey) {
                    $catName = optional($item->cursor->collection)->base_name_en;
                } else {
                    $catName = $catTranslated;
                }

             //   $this->info("🌍 Категорія: $catName");

                $documents[] = [
                    'id' => $item->cursor_id,
                    'name' => $name,
                    'tags' => $item->tags,
                    'lang' => $lang,
                    'isFallback' => $item->lang !== $lang ? true : false, // 🆕
                    'cat' => optional($item->cursor->collection)->alt_name,
                    'catid' => optional($item->cursor->collection)->id,
                    'cat_name' => $catName,
                    'cat_img' => optional($item->cursor->collection)->img,
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
                $hosts = [
                    'http://localhost:7700',
                    'http://meilisearch:7700',
                ];

                $response = null;

                foreach ($hosts as $host) {
                    try {
                        if ($force) {
                            Http::withHeaders([
                                'Authorization' => 'Bearer masterKey123',
                            ])->delete("{$host}/indexes/cursors_{$lang}");

                            $this->line("🧹 Індекс [$lang] очищено на {$host}");

                            // 🆕 Явне створення індексу з primaryKey
                            Http::withHeaders([
                                'Authorization' => 'Bearer masterKey123',
                                'Content-Type' => 'application/json',
                            ])->put("{$host}/indexes/cursors_{$lang}", [
                                'uid' => "cursors_{$lang}",
                                'primaryKey' => 'id',
                            ]);

                            $this->line("📦 Індекс [$lang] заново створено з primaryKey 'id'");
                        }

                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer masterKey123',
                            'Content-Type' => 'application/json',
                        ])->timeout(3)->post("{$host}/indexes/cursors_{$lang}/documents", $documents);

                        if ($response->successful()) {
                            $this->info("✅ Завантажено " . count($documents) . " курсорів у індекс [$lang] через {$host}\n");
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!$response || $response->failed()) {
                    $this->error("❌ Помилка для мови [$lang] — жоден Meilisearch не відповів.");
                }
            } else {
                $this->info("⚠️ Немає тегів для мови [$lang]\n");
            }
        }

        $this->info("\n🎉 Завершено додавання курсорів у Meilisearch для всіх мов.\n");
        return 0;
    }
}
