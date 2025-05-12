<?php

namespace App\Http\Controllers;

use \Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use DB;
use Validator;
use App\uninstalled;
use Carbon\Carbon;
use App\Models\cursor;
use App\Models\categories;
use App\Models\Analytic;
use App\Models\CursorTranslation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


use App;

class IndexController extends Controller
{

    public $alt_name, $cat_id;

    public function debugPath()
    {
        return view('debug');
    }

    public function getCursor(Request $r)
    {
        dd($r);
    }

    public function cleanStr($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string));
        return $string;
    }

    public function showCursorPreview(Request $r)
    {
        if (!$r->id) {
            abort(404);
        }

        $excludeId = isset($_COOKIE['hide_item_2082']) && $_COOKIE['hide_item_2082'] === 'true'
            ? 100000000 : 2082;

        $after = cursor::whereDate('schedule', '<=', Carbon::today())
            ->where('id', '<>', $excludeId)
            ->where('id', '>=', $r->id)
            ->orderBy('id', 'ASC')
            ->limit(2)
            ->get();

        $before = cursor::whereDate('schedule', '<=', Carbon::today())
            ->where('id', '<>', $excludeId)
            ->where('id', '<', $r->id)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        $cursors2 = $before->merge($after);
        $cursors2->load('currentTranslation');

        $collections = categories::with('currentTranslation')->get();

        foreach ($cursors2 as $cursorItem) {
            $cursorItem->collection = $collections->first(fn($item) => $item->id == $cursorItem->cat);

            $seoCategory = Str::slug($cursorItem->collection->base_name_en);
            $seoCursor = Str::slug($cursorItem->currentTranslation->name ?? $cursorItem->name_en);
            if (empty($seoCursor)) {
                $seoCursor = Str::slug($cursorItem->name_en);
            }
            $fullSlug = "https://cursor.style/collections/{$seoCategory}/{$cursorItem->id}-{$seoCursor}";

            $cursorItem->slug_url_final = $fullSlug;
            $cursorItem->c_file_no_ext = $fullSlug . '-cursor';
            $cursorItem->p_file_no_ext = $fullSlug . '-pointer';
            $cursorItem->name_s = Str::slug($cursorItem->name_en);
        }

        $id_prev = null;
        $id_next = null;

        foreach ($cursors2 as $key => $cursor) {
            if ($key === 0) {
                $id_prev = [$cursor->id, $cursor->name_s];
            }
            if ($key === 2) {
                $id_next = [$cursor->id, $cursor->name_s];
            }
        }

        $random_cat = $collections->random(3);

        if ($cursors2->isEmpty() || !isset($cursors2[1])) {
            abort(404);
        }


        // 🆕 Отримуємо всі курсори з категорії цього курсора
        $cursor = $cursors2[1];   
        $category_cursors = collect();
        if ($cursor) {
            $category_cursors = cursor::whereDate('schedule', '<=', Carbon::today())
                ->where('cat', $cursor->cat)
                ->where('id', '<>', $excludeId)
                ->orderBy('id')
                ->get();

            foreach ($category_cursors as $catCursor) {
                $catCursor->currentTranslation;
                $catCursor->collection = $collections->first(fn($item) => $item->id == $catCursor->cat);

                $seoCategory = Str::slug($catCursor->collection->base_name_en);
                $seoCursor = Str::slug($catCursor->currentTranslation->name ?? $catCursor->name_en);
                if (!$seoCursor) {
                    $seoCursor = Str::slug($catCursor->name_en);
                }

                $fullSlug = "https://cursor.style/collections/{$seoCategory}/{$catCursor->id}-{$seoCursor}";

                $catCursor->slug_url_final = $fullSlug;
                $catCursor->c_file_no_ext = $fullSlug . '-cursor';
                $catCursor->p_file_no_ext = $fullSlug . '-pointer';
                $catCursor->name_s = Str::slug($catCursor->name_en);
            }
        }

        
        return response()
            ->view('cursor', [
                'random_cat' => $random_cat,
                'all_cursors' => $cursors2,
                'cursor' => $cursor,
                'id_prev' => $id_prev,
                'id_next' => $id_next,
                'category_cursors' => $category_cursors, // 🔥 новий список
            ])->header('Cache-Tag', 'details');
    }

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
    
        return cursor::whereIn('id', $ids)
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
            $cursorModels = cursor::whereIn('id', $ids)
                ->whereDate('schedule', '<=', now())
                ->where('id', '<>', $excludeId)
                ->get();
    
            $collections = categories::all();
    
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
            $collections = categories::all();
    
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
    
        
    public function show(Request $r)
    {
        $order = 'desc';
        $sort = 'id';
    
        if ($r->type === 'popular') {
            $sort = 'todayClick';
            $order = 'desc';
        } elseif ($r->type === 'new') {
            $sort = 'id';
            $order = 'desc';
        }
    
        $query = $r->q;
        $excludeId = isset($_COOKIE['hide_item_2082']) && $_COOKIE['hide_item_2082'] === 'true' ? 100000000 : 2082;
    
        if ($query) {
            $ids = CursorTranslation::where('name', 'LIKE', "%{$query}%")
                ->pluck('cursor_id')
                ->unique();
    
            $cursors = cursor::whereIn('id', $ids)
                ->whereDate('schedule', '<=', Carbon::today())
                ->where('id', '<>', $excludeId)
                ->orderBy($sort, $order)
                ->paginate(32);
            $cursors->load('currentTranslation');
        } else {
            $cursors = cursor::whereDate('schedule', '<=', Carbon::today())
                ->orderBy($sort, $order)
                ->where('id', '<>', $excludeId)
                ->paginate(32);
            $cursors->load('currentTranslation');                
        }
    
        $collections = categories::all();

        foreach ($cursors as $cursorItem) {
            // Просто додаємо колекцію як тимчасову властивість (не для збереження)
            $cursorItem->setRelation('collection', $collections->first(
                fn($item) => $item->id == $cursorItem->cat
            ));
        
            // Генерація SEO-шляху
            $seoCategory = Str::slug($cursorItem->collection->base_name_en);
            $seoCursor = Str::slug($cursorItem->currentTranslation->name ?? $cursorItem->name_en);
            if (!$seoCursor){
                $seoCursor = Str::slug($cursorItem->name_en);
            }
            $fullSlug = "https://cursor.style/collections/{$seoCategory}/{$cursorItem->id}-{$seoCursor}";
                
            $cursorItem->slug_url_final = $fullSlug;
            $cursorItem->c_file_no_ext = $fullSlug . '-cursor';
            $cursorItem->p_file_no_ext = $fullSlug . '-pointer';
        }
        
    
        $response = response()->view('index', [
            'cursors' => $cursors,
            'query' => $query,
            'sort' => $sort
        ])->header('Cache-Tag', 'index');
    
        $response->headers->remove('Cache-Control');
        return $response;
    }

    public function showJsLang()
    {
        $lang = config('app.locale');

        $files = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $strings[$name] = require $file;
        }
        $strings = json_encode($strings);

        header('Content-Type: text/javascript');
        echo ('window.i18n = ' . $strings . ';');
        exit();
    }

    public function sendEmailFeedback(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:100',
        ]);

        $message = $request->input('message');

        // Асинхронно відправляємо запит до Lambda
        $response = Http::post('https://i6bnl4iutvwekmi6ziw5vi7bxi0hwclp.lambda-url.us-east-1.on.aws', [
            'message' => $message,
        ]);

        // Одразу повертаємо відповідь користувачу
        return redirect()->back()->with('success', true);
    }

    public function getAll()
    {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top')->join('categories', 'cursors.cat', '=', 'categories.id')->whereDate('schedule', '<=', Carbon::today())->get();
        return ['data' => $value];
    }

    public function getAllOrderById()
    {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top')->join('categories', 'cursors.cat', '=', 'categories.id')->whereDate('schedule', '<=', Carbon::today())->orderby('cursors.id')->get();
        return ['data' => $value];
    }

    public function getAllAnimatedOrderById()
    {
        $value = DB::table('animateds')->select('id', 'name', 'c_file', 'p_file', 'c_file_prev', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p')->orderby('id')->get();
        return $value;
    }

    public function getAllByCat($cat)
    {
        $value = DB::table('cursors')->select('cursors.id', 'name', 'c_file', 'p_file', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p', 'cat', 'top', 'cursors.created_at', 'base_name', 'alt_name')
            ->join('categories', 'cursors.cat', '=', 'categories.id')
            ->whereDate('schedule', '<=', Carbon::today())
            ->where('alt_name', $cat)->orderby('id', 'desc')->get();
        return $value;
    }

    public function getAllCategories()
    {
        $value = DB::table('categories')->select('id', 'base_name', 'alt_name', 'priority', 'description', 'short_descr', 'img')->orderBy('id', 'desc')->get();
        return ['data_cat' => $value];
    }

    public function getCategories($cat)
    {
        $value = DB::table('categories')->select('*')->where('alt_name', $cat)->get();

        return $value;
    }

    public function getCategoriesRandom($cat)
    {
        return DB::table('categories')->select('*')->where('alt_name', '!=', $cat)->where('id', '<>', '29000')->inRandomOrder()->limit(3)->get();
    }

    public function getAllAnimated()
    {

        $value = DB::table('animateds')->select('id', 'name', 'c_file', 'p_file', 'c_file_prev', 'p_file_prev', 'offsetX', 'offsetY', 'offsetX_p', 'offsetY_p')->get();

        $value = collect(json_decode($value));

        return $value;
    }
    public function showCat($alt_name, Request $r)
    {
        $this->alt_name = $alt_name;
    
        // Колекція з перекладом
        $collection = categories::with('currentTranslation')
            ->where('alt_name', $alt_name)
            ->first();
    
        if (!$collection) {
            abort(404);
        }
    
        $excludeId = isset($_COOKIE['hide_item_2082']) && $_COOKIE['hide_item_2082'] === 'true'
            ? 100000000 : 2082;
    
        // Курсори до цієї колекції
        $items = cursor::whereDate('schedule', '<=', now())
            ->where('cat', $collection->id)
            ->where('id', '<>', $excludeId)
            ->orderBy('id')
            ->get();
    
        // Всі колекції з перекладами (для random_cat)
        $collections = categories::with('currentTranslation')->get();
    
        foreach ($items as $cursor) {
            $cursor->seo_link = $cursor->id . '/' . strtolower($alt_name) . '/' .
                strtolower(transliterator_transliterate('Russian-Latin/BGN', str_replace(' ', '_', $cursor->name)));

            $cursor->name_s = $this->cleanStr(string: $cursor->name_en);
            $cursor->setRelation('collection', $collections->firstWhere('id', $cursor->cat));

            $seoCategory = Str::slug($cursor->collection->base_name_en);
            $seoCursor = Str::slug($cursor->currentTranslation->name ?? $cursor->name_en);
            if (!$seoCursor){
                $seoCursor = Str::slug($cursor->name_en);
            }
            $fullSlug = "https://cursor.style/collections/{$seoCategory}/{$cursor->id}-{$seoCursor}";

            $cursor->slug_url_final = $fullSlug;
            $cursor->c_file_no_ext = $fullSlug . '-cursor';
            $cursor->p_file_no_ext = $fullSlug . '-pointer';
        }
    
        $random_cat = $collections->random(3);
    
        return response()->view('cat', [
            'alt_name' => $alt_name,
            'cursors' => $items,
            'collection' => $collection,
            'random_cat' => $random_cat
        ])->header('Cache-Tag', 'collection');
    }
    

    public function showAllCat2()
    {
        $cats = DB::table('categories')
            ->select('id', 'base_name', 'alt_name', 'priority', 'description', 'short_descr', 'img')
            ->orderBy('id', 'desc')
            ->paginate(20);
    
        return view('allcat', compact('cats'));
    }
    

    public function showAllCat()
    {
        $cats = categories::with('currentTranslation')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()
            ->view('allcat', compact('cats'))
            ->header('Cache-Tag', 'collections');
    }
        
            public function setTopCursor(Request $request) {
        //        if ($request->type == 'stat') {
        //            $db = cursor::find($request->id);
        //            $db->top = $db->top + 1;
        //            $db->save();
        //        }
            }
        
            public function successInstall(Request $request) {
        //        if ($request->session()->has('installed')) {
        //        } else {
        //            $request->session()->put('installed', date("Ymd"));
        //            $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
        //            $stat->date = \Carbon\Carbon::now();
        //            $stat->increment('installs');
        //            $stat->save();
        //        }
                return view('other.success');
            }
        
            public function update() {
        //        $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
        //        $stat->date = \Carbon\Carbon::now();
        //        $stat->increment('response');
        //        $stat->save();
                return ['result' => true];
            }
        
            public function showFeedback(Request $request) {
                return response()
                ->view('feedback')
                ->header('Cache-Tag', 'feedback');
            }    

    public function mycollection(Request $request)
    {
        return view('mycollection');
    }
}
