<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CursorTagTranslation;
use App\Support\CollectionPresenter;
use App\Support\CursorPresenter;

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
            app()->setLocale($lang);
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

            $this->info("🔍 Знайдено " . $tagged->count() . " записів для [$lang]");
            $documents = [];

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

                // --- Додаємо SEO URL-и
                $seoCursor = CursorPresenter::seo($item->cursor); // має повертати slug, slug_url_final тощо
                $seoCollection = null;
                if ($item->cursor->collection) {
                    $seoCollection = CollectionPresenter::seo($item->cursor->collection);
                    $collection_url = route('collection.show', [
                        'id' => $item->cursor->collection,
                        'slug' => $seoCollection['trans'],
                    ]);                      
                }
            

                $item->cursor->details_url = route('collection.cursor.details', [
                    'cat' => $item->cursor->cat,
                    'collection_slug' => $seoCursor['catTrans'],
                    'id' => $item->cursor->id,
                    'cursor_slug' => $seoCursor['cursorTrans'],
                ]);

                $documents[] = [
                    'id' => $item->cursor_id,
                    'name' => $name,
                    'tags' => $item->tags,
                    'lang' => $lang,
                    'isFallback' => $item->lang !== $lang ? true : false,
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
                    // Нові поля ↓↓↓
                    'cursor_url' => $item->cursor->details_url ?? '', // або details_url, якщо є
                    'collection_url' => $collection_url ?? '',
                ];
            }

            $this->info("🔍 До імпорту: " . count($documents) . " документів для [$lang]");

            if (!empty($documents)) {
                $hosts = [
                    'http://localhost:7700',
                    'http://meilisearch:7700',
                ];

                // Якщо --force, дропаємо індекс на всіх хостах
                if ($force) {
                    foreach ($hosts as $host) {
                        try {
                            Http::withHeaders([
                                'Authorization' => 'Bearer masterKey123',
                            ])->delete("{$host}/indexes/cursors_{$lang}");
                            $this->line("🧹 Індекс [$lang] очищено на {$host}");
                        } catch (\Exception $e) {
                            // Може бути 404 — ок
                        }
                    }
                }

                // Створити індекс тільки якщо нема (перевірка по 404)
                $indexCreated = false;
                foreach ($hosts as $host) {
                    try {
                        $getResponse = Http::withHeaders([
                            'Authorization' => 'Bearer masterKey123',
                        ])->get("{$host}/indexes/cursors_{$lang}");

                        if ($getResponse->status() === 404) {
                            // POST, а не PUT!
                            Http::withHeaders([
                                'Authorization' => 'Bearer masterKey123',
                                'Content-Type' => 'application/json',
                            ])->post("{$host}/indexes", [
                                'uid' => "cursors_{$lang}",
                                'primaryKey' => 'id',
                            ]);
                            $this->line("📦 Індекс [$lang] створено з primaryKey 'id' через {$host}");
                        } else {
                            $this->line("ℹ️ Індекс [$lang] вже існує на {$host}");
                        }
                        $indexCreated = true;
                        $activeHost = $host;
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!$indexCreated) {
                    $this->error("❌ Не вдалося створити або знайти індекс для [$lang] — пропускаємо цю мову.");
                    continue;
                }

                // Заливати батчами по 500 для стабільності
                $chunks = array_chunk($documents, 500);
                foreach ($chunks as $chunk) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer masterKey123',
                            'Content-Type' => 'application/json',
                        ])->timeout(10)->post("{$activeHost}/indexes/cursors_{$lang}/documents", $chunk);

                        if ($response->successful()) {
                            $this->info("✅ Завантажено батч з " . count($chunk) . " курсорів у індекс [$lang] через {$activeHost}");
                        } else {
                            $this->error("❌ Помилка завантаження батча у індекс [$lang] через {$activeHost}");
                            // $this->error($response->body());
                        }
                    } catch (\Exception $e) {
                        $this->error("❌ Exception при додаванні батча у індекс [$lang]: " . $e->getMessage());
                    }
                }
            } else {
                $this->info("⚠️ Немає тегів для мови [$lang]\n");
            }
        }

        $this->info("\n🎉 Завершено додавання курсорів у Meilisearch для всіх мов.\n");
        return 0;
    }
}
