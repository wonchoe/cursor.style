<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use DB;
use App\Models\Cursors;
use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


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
                Log::info("🔎 Запит до {$host}/indexes/cursors_{$lang}/search");
                Log::info('📦 Тіло запиту: ' . json_encode([
                    'q' => $query,
                    'limit' => $limit,
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
                    Log::info("✅ Успішна відповідь від {$host}");
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
    
        try {
            $response = $this->miliRequest($lang, $query, $limit);
    
            if ($response->failed()) {
                throw new \Exception("Meilisearch request failed");
            }
    
            $ids = collect($response->json()['hits'])->pluck('id')->toArray();
    
            $excludeId = ($_COOKIE['hide_item_2082'] ?? null) === 'true' ? 100000000 : 2082;
            $cursorModels = Cursors::whereIn('id', $ids)
                ->whereDate('schedule', '<=', now())
                ->where('id', '<>', $excludeId)
                ->get();
    
            $collections = Collection::all();
    
            $cursors = collect($ids)->map(function ($id) use ($cursorModels, $collections) {
                $cursor = $cursorModels->firstWhere('id', $id);
                if (!$cursor) return null;
    
                $cursor->name_s = Str::slug($cursor->name_en);
                $collection = $collections->first(fn($item) => $item->id == $cursor->cat);
                $cursor->setRelation('collection', $collection);
    
                if ($collection) {
                    $seoCategory = Str::slug($collection->base_name_en);
                    $seoCursor = Str::slug($cursor->name_en);
                    $fullSlug = "collections/{$seoCategory}/{$cursor->id}-{$seoCursor}";
    
                    $cursor->slug_url_final = $fullSlug;
                    $cursor->c_file_no_ext = $fullSlug . '-cursor';
                    $cursor->p_file_no_ext = $fullSlug . '-pointer';
                }
    
                return $cursor;
            })->filter();
    
            $sort = 'search';
    
        } catch (\Throwable $e) {
            logger()->warning('Meilisearch error: ' . $e->getMessage());
    
            $cursors = $this->searchFallback($query, $lang);
            $collections = Collection::all();
    
            foreach ($cursors as $cursorItem) {
                $cursorItem->name_s = Str::slug($cursorItem->name_en);
                $collection = $collections->first(fn($item) => $item->id == $cursorItem->cat);
                $cursorItem->setRelation('collection', $collection);
    
                if ($collection) {
                    $seoCategory = Str::slug($collection->base_name_en);
                    $seoCursor = Str::slug($cursorItem->name_en);
                    $fullSlug = "collections/{$seoCategory}/{$cursorItem->id}-{$seoCursor}";
    
                    $cursorItem->slug_url_final = $fullSlug;
                    $cursorItem->c_file_no_ext = $fullSlug . '-cursor';
                    $cursorItem->p_file_no_ext = $fullSlug . '-pointer';
                }
            }
    
            $sort = 'fallback';
        }
    
        return response()
            ->view('index', [
                'cursors' => $cursors,
                'query' => $query,
                'sort' => $sort,
            ])
            ->header('Cache-Tag', 'index')
            ->withoutCookie('Cache-Control');
    }
}    