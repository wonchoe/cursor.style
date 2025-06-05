<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use DB;
use App\Models\Cursors;
use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Support\CursorPresenter;

use App;

class SearchController extends Controller
{
private function searchFallback(string $query, string $lang = 'en'): \Illuminate\Support\Collection
    {
        // Спрощуємо тег — прибираємо всі зайві символи
        $normalized = strtolower(preg_replace('/[^a-z0-9\s]/iu', '', $query));
    
        // Пошук по тегам
        $ids = DB::table('cursor_tag_translations')
            ->where('lang', $lang)
            ->where('tags', 'LIKE', '%' . $normalized . '%')
            ->pluck('cursor_id')
            ->unique();
    
        return Cursors::whereIn('id', $ids)
            ->whereDate('schedule', '<=', now())
            ->get();
    }

    public function searchProxy(Request $request)
    {
        $lang = app()->getLocale();
        $query = $request->input('query'); // 👈 не q
        $limit = $request->input('limit', 100);
    
        $response = $this->miliRequest($lang, $query, $limit);
        return response()->json($response->json());
    }

    public function miliRequest($lang, $query, $limit)
    {

        $supportedLanguages = [
            'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
            'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
            'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
        ];

        if (!in_array($lang, $supportedLanguages)) {
            Log::warning("🌐 Мова '$lang' не підтримується — fallback на 'en'");
            $lang = 'en';
        }

        $hosts = [
            'http://localhost:7700',
            'http://meilisearch:7700'            
        ];
    
        // Завжди гарантуємо, що q — це рядок
        $query = (string) $query;
    
        foreach ($hosts as $host) {
            try {
                Log::info('📦 Тіло запиту: ' . json_encode([
                    $lang => $query
                ]));
    
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer masterKey123',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(2)
                ->withBody(json_encode([
                    'q' => $query,
                    'limit' => $limit,
                ]), 'application/json')
                ->post("{$host}/indexes/cursors_{$lang}/search");
    
                if ($response->successful()) {
                    return $response;
                } else {
                    Log::warning("⚠️ Статус: " . $response->status() . "; Тіло: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("❌ Виняток при зверненні до {$host}: " . $e->getMessage());
            }
        }
    
        throw new \Exception("❌ Meilisearch is unavailable on all hosts");
    }
    

    public function search($q, Request $request)
    {
        $lang = app()->getLocale();
        $query = $q;
        $limit = 100;

        if (!$query) return redirect('/');

        $collections = Collection::with('currentTranslation')->get();

        try {
            $response = $this->miliRequest($lang, $query, $limit);

            if ($response->failed()) {
                throw new \Exception("Meilisearch request failed");
            }

            $ids = collect($response->json()['hits'])->pluck('id')->toArray();

            $excludeId = ($_COOKIE['hide_item_2082'] ?? null) === 'true' ? 100000000 : 2082;

            // Підтягуємо курсори одразу з currentTranslation
            $cursorModels = Cursors::whereIn('id', $ids)
                ->whereDate('schedule', '<=', now())
                ->where('id', '<>', $excludeId)
                ->with('currentTranslation')
                ->get();

            // Перетворюємо список id в колекцію курсорів у потрібному порядку
            $cursors = collect($ids)->map(function ($id) use ($cursorModels, $collections) {
                $cursor = $cursorModels->firstWhere('id', $id);
                if (!$cursor) return null;

                // Назва в slug-форматі
                $cursor->name_s = slugify($cursor->name_en);

                // Колекція
                $collection = $collections->first(fn($item) => $item->id == $cursor->cat);
                $cursor->setRelation('collection', $collection);

                // SEO, slug-и, файли
                if ($collection) {
                    $seo = CursorPresenter::seo($cursor);
                    $cursor->detailsSlug = $seo['detailsSlug'];
                    $cursor->collectionSlug = $seo['collectionSlug'];
                    $cursor->c_file = $seo['c_file'];
                    $cursor->p_file = $seo['p_file'];
                    $cursor->details_url = route('collection.cursor.details', [
                        'cat' => $cursor->cat,
                        'collection_slug' => $seo['catTrans'],
                        'id' => $cursor->id,
                        'cursor_slug' => $seo['cursorTrans'],
                    ]);                      
                }

                // Теги для поточної мови (опціонально)
                $tagTranslation = \App\Models\CursorTagTranslation::where('cursor_id', $cursor->id)
                    ->where('lang', app()->getLocale())
                    ->first();
                if ($tagTranslation) {
                    $cursor->tags = explode(' ', $tagTranslation->tags);
                }

                // Можна додати ще інші поля, якщо використовуєш їх у шаблоні (наприклад, seo_title, seo_description...)

                return $cursor;
            })->filter();

            $sort = 'search';

        } catch (\Throwable $e) {
            logger()->warning('Meilisearch error: ' . $e->getMessage());

            // Фолбек логіка (аналогічно заповнюємо курсори як вище)
            $cursors = $this->searchFallback($query, $lang);
            $collections = Collection::with('currentTranslation')->get();

            $cursors = collect($cursors)->map(function ($cursor) use ($collections) {
                $cursor->name_s = Str::slug($cursor->name_en);

                $collection = $collections->first(fn($item) => $item->id == $cursor->cat);
                $cursor->setRelation('collection', $collection);

                if ($collection) {
                    $seo = CursorPresenter::seo($cursor);
                    $cursor->detailsSlug = $seo['detailsSlug'];
                    $cursor->collectionSlug = $seo['collectionSlug'];
                    $cursor->c_file = $seo['c_file'];
                    $cursor->p_file = $seo['p_file'];
                    $cursor->details_url = route('collection.cursor.details', [
                        'cat' => $cursor->cat,
                        'collection_slug' => $seo['catTrans'],
                        'id' => $cursor->id,
                        'cursor_slug' => $seo['cursorTrans'],
                    ]);                     
                }

                $tagTranslation = \App\Models\CursorTagTranslation::where('cursor_id', $cursor->id)
                    ->where('lang', app()->getLocale())
                    ->first();
                if ($tagTranslation) {
                    $cursor->tags = explode(' ', $tagTranslation->tags);
                }

                return $cursor;
            })->filter();

            $sort = 'fallback';
        }

        return response()
            ->view('index', [
                'cursors' => $cursors,
                'query' => $query,
                'sort' => $sort,
                'isSearch' => true
            ])
            ->header('Cache-Tag', 'search')
            ->withoutCookie('Cache-Control');
    }

}    