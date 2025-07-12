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
    protected $description = 'Sync only new cursors to Meilisearch for all languages';

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    protected $meiliHosts = [
        'http://localhost:7700',
        'http://meilisearch:7700',
    ];

    protected $meiliApiKey = 'masterKey123';

    public function handle()
    {
        function route_path($name, $parameters = [])
        {
            $url = route($name, $parameters, false);
            return parse_url($url, PHP_URL_PATH);
        }

        $force = $this->option('force');

        $this->info("\n✨ Синхронізуємо курсори у Meilisearch для всіх мов...");

        foreach ($this->languages as $lang) {
            app()->setLocale($lang);
            $this->info("🌍 Мова: $lang");
            $index = "cursors_{$lang}";

            // 1. Drop index if --force, дочекайся завершення таску!
            if ($force) {
                foreach ($this->meiliHosts as $host) {
                    try {
                        $deleteResponse = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->meiliApiKey,
                        ])->delete("{$host}/indexes/{$index}");

                        $deleteTaskId = $deleteResponse->json()['taskUid'] ?? null;
                        if ($deleteTaskId) {
                            $this->line("🧹 Видаляємо індекс [$lang] на {$host}, очікуємо завершення...");
                            // Чекаємо поки видалення завершиться
                            do {
                                usleep(250 * 1000); // 0.25 секунди пауза
                                $taskStatus = Http::withHeaders([
                                    'Authorization' => 'Bearer ' . $this->meiliApiKey,
                                ])->get("{$host}/tasks/{$deleteTaskId}")->json();
                                $status = $taskStatus['status'] ?? '';
                            } while ($status !== 'succeeded' && $status !== 'failed');
                            $this->line("🗑 Індекс [$lang] видалено на {$host} (статус: $status)");
                        }
                    } catch (\Exception $e) {
                        // 404 — ок, якщо індекс не існує
                    }
                }
            }

            // 2. Створити індекс якщо нема (перевірка по 404)
            $indexCreated = false;
            foreach ($this->meiliHosts as $host) {
                try {
                    $getResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->meiliApiKey,
                    ])->get("{$host}/indexes/{$index}");

                    if ($getResponse->status() === 404) {
                        $createResponse = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->meiliApiKey,
                            'Content-Type' => 'application/json',
                        ])->post("{$host}/indexes", [
                            'uid' => $index,
                            'primaryKey' => 'id',
                        ]);
                        $this->line("📦 Індекс [$lang] створено на {$host}");
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

            // 3. Витягуємо всі id з Meili (limit 2000, бо <=1500 курсорів)
            $meiliIds = [];
            $limit = 1000;
            $offset = 0;
            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->meiliApiKey,
                ])->get("{$activeHost}/indexes/{$index}/documents", [
                    'fields' => 'id',
                    'limit' => $limit,
                    'offset' => $offset,
                ]);
                $meiliIdsRaw = $response->json();
                $docs = $meiliIdsRaw['results'] ?? $meiliIdsRaw;
                $batch = collect($docs)->pluck('id')->map(fn($id) => (string)$id)->toArray();

                $meiliIds = array_merge($meiliIds, $batch);
                $offset += $limit;
            } while (count($batch) === $limit); // Якщо останній батч менше limit — це кінець

            // 4. Всі id з бази
            $dbIdsRaw = CursorTagTranslation::where('lang', $lang)->pluck('cursor_id')->toArray();
            $dbIds = array_map('strval', $dbIdsRaw);

            $this->line("Meili ids: " . implode(',', array_slice($meiliIds, 0, 10)) . ' ...');
            $this->line("DB ids: " . implode(',', array_slice($dbIds, 0, 10)) . ' ...');

            // 5. Знаходимо тільки відсутні у Meili
            $missingIds = array_diff($dbIds, $meiliIds);

            $this->line("Missing ids: " . implode(',', array_slice($missingIds, 0, 10)) . ' ... [' . count($missingIds) . ']');

            if (count($missingIds) == 0) {
                $this->info("✅ Всі курсори для [$lang] вже є, скіпаємо.");
                continue;
            }

            $this->info("🔍 Додаємо " . count($missingIds) . " нових курсорів для [$lang]...");

            // 6. Витягуємо потрібні CursorTagTranslation
            $tagged = CursorTagTranslation::with('cursor.collection', 'cursor')
                ->where('lang', $lang)
                ->whereIn('cursor_id', $missingIds)
                ->get();

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

                $seoCursor = CursorPresenter::seo($item->cursor);
                $seoCollection = null;
                if ($item->cursor->collection) {
                    $seoCollection = CollectionPresenter::seo($item->cursor->collection);
                    $collection_url = route_path('collection.show', [
                        'id' => $item->cursor->collection,
                        'slug' => $seoCollection['trans'],
                    ]);
                }

                $item->cursor->details_url = route_path('collection.cursor.details', [
                    'cat' => $item->cursor->cat,
                    'collection_slug' => $seoCursor['catTrans'],
                    'id' => $item->cursor->id,
                    'cursor_slug' => $seoCursor['cursorTrans'],
                ]);

                $img = str_replace('.png', '.webp', optional($item->cursor->collection)->img);
                $img = str_replace('.svg', '.webp', optional($item->cursor->collection)->img);

                $documents[] = [
                    'id' => (string)$item->cursor_id, // тип string для сумісності!
                    'name' => $name,
                    'tags' => $item->tags,
                    'lang' => $lang,
                    'isFallback' => $item->lang !== $lang ? true : false,
                    'cat' => optional($item->cursor->collection)->alt_name,
                    'catid' => optional($item->cursor->collection)->id,
                    'cat_name' => $catName,
                    'cat_img' => $img,
                    'c_file' => $item->cursor->c_file,
                    'p_file' => $item->cursor->p_file,
                    'offsetX' => $item->cursor->offsetX,
                    'offsetY' => $item->cursor->offsetY,
                    'offsetX_p' => $item->cursor->offsetX_p,
                    'offsetY_p' => $item->cursor->offsetY_p,
                    'created_at' => $item->cursor->created_at->toDateTimeString(),
                    'cursor_url' => $item->cursor->details_url ?? '',
                    'collection_url' => $collection_url ?? '',
                ];
            }

            // 7. Батчимо і заливаємо
            $chunks = array_chunk($documents, 500);
            foreach ($chunks as $chunk) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->meiliApiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout(10)->post("{$activeHost}/indexes/{$index}/documents", $chunk);

                    if ($response->successful()) {
                        $this->info("✅ Завантажено батч з " . count($chunk) . " курсорів у індекс [$lang] через {$activeHost}");
                    } else {
                        $this->error("❌ Помилка завантаження батча у індекс [$lang] через {$activeHost}");
                    }
                } catch (\Exception $e) {
                    $this->error("❌ Exception при додаванні батча у індекс [$lang]: " . $e->getMessage());
                }
            }
        }

        $this->info("\n🎉 Синхронізація завершена для всіх мов.\n");
        return 0;
    }
}
