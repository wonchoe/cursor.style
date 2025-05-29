<?php
namespace App\Http\Controllers;

use App\Models\Cursors;
use App\Models\CursorTranslation;
use App\Models\CollectionTranslation;
use App\Models\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\CursorPresenter;
use App\Support\CollectionPresenter;
use Illuminate\Support\Facades\Http; 

class IndexController extends Controller
{
    public function index(Request $request, $type = null)
    {
        $query = $request->input('q');
        $sort = ($request->input('type') === 'popular' || $type === 'popular') ? 'todayClick' : 'id';        
        $order = 'desc';

        $excludeId = ($request->cookie('hide_item_2082') === 'true') ? 100000000 : 2082;

        $cursorQuery = Cursors::query()
            ->whereDate('schedule', '<=', Carbon::today())
            ->where('id', '<>', $excludeId)
            ->orderBy($sort, $order);

        if ($query) {
            $ids = CursorTranslation::where('name', 'LIKE', "%{$query}%")
                ->pluck('cursor_id')
                ->unique();

            $cursorQuery->whereIn('id', $ids);
        }

        $cursors = $cursorQuery
            ->with(['currentTranslation', 'Collection.currentTranslation'])->paginate(33);

        foreach ($cursors as $cursor) {
            $seo = CursorPresenter::seo($cursor);
            $cursor->slug_url_final = $seo['slug_url_final'];
            $cursor->catTrans = $seo['catTrans'];
            $cursor->cursorTrans = $seo['cursorTrans'];            
            $cursor->collectionSlug = $seo['collectionSlug'];
            $cursor->c_file = $seo['c_file'];
            $cursor->p_file = $seo['p_file'];
            $cursor->details_url = route('collection.cursor.details', [
                'cat' => $cursor->cat,
                'collection_slug' => $cursor->catTrans,
                'id' => $cursor->id,
                'cursor_slug' => $cursor->cursorTrans,
            ]);  
        }

        return response()
            ->view('index', [
                'cursors' => $cursors,
                'query' => $query,
                'sort' => $sort
            ])
            ->header('Cache-Tag', 'index');
    }


    public function detailsLegacy($id, $name)
    {
        // 1. Дістаємо курсор і колекцію з бази
        $cursor = Cursors::with('collection')->findOrFail($id);
        $collection = $cursor->collection;
        // Отримуємо переклад для поточної мови, або беремо англійську
        $lang = app()->getLocale();
        $translations = $cursor->translations->pluck('name', 'lang')->toArray();
        $translationsCat = $collection->translations->pluck('name', 'lang')->toArray();

        // Генеруємо slug'и (як у новій структурі)
        $catSlug = isset($translationsCat[$lang]) ? slugify($translationsCat[$lang]) : slugify($collection->base_name_en);
        $cursorSlug = isset($translations[$lang]) ? slugify($translations[$lang].' cursor') : slugify($cursor->name_en.' cursor');

        // 2. Формуємо новий URL з правильними ключами!
        $url = route('collection.cursor.details', [
            'cat' => $collection->id,
            'collection_slug' => $catSlug,
            'id' => $cursor->id,
            'cursor_slug' => $cursorSlug,
        ]);

        // 3. Повертаємо 301 редірект
        return redirect()->to($url, 301);
    }


    public function details(Request $r)
    {
        // $cursor_slug = $r->cursor_slug;
        // $collection_slug = $r->collection_slug;
        // $r->id = explode('-',$cursor_slug)[0];
        // $r->cat = explode('-',$collection_slug)[0];

        if ((!$r->id) or (!$r->cat)) {
            abort(404);
        }


        $excludeId = $r->cookie('hide_item_2082') === 'true' ? 100000000 : 2082;
        $baseQuery = Cursors::whereDate('schedule', '<=', now())
            ->where('id', '<>', $excludeId)->with('currentTranslation');

        $collections = Collection::with('currentTranslation')->get();

        // Current cursor
        $cursor = (clone $baseQuery)
            ->where('id', $r->id)
            ->with(['currentTranslation', 'collection.currentTranslation'])
            ->firstOrFail();
            

        $translations = CursorTranslation::where('cursor_id', $r->id)->pluck('name', 'lang')->toArray();
        $translationsCat = CollectionTranslation::where('collection_id', $r->cat)->pluck('name', 'lang')->toArray();

        // Додаємо описи з seo_cursor_texts для поточної мови
        $seoText = \App\Models\SeoCursorText::where('cursor_id', $cursor->id)
            ->where('lang', app()->getLocale())
            ->first();

        if ($seoText) {
            $cursor->seo_title = $seoText->seo_title;
            $cursor->seo_description = $seoText->seo_description;
            $cursor->seo_page = $seoText->seo_page;
        }
        
        $seo = CursorPresenter::seo($cursor);
        $cursor->detailsSlug = $seo['detailsSlug'];
        $cursor->collectionSlug = $seo['collectionSlug'];        
        $cursor->c_file = $seo['c_file'];
        $cursor->p_file = $seo['p_file'];
        $cursor->short_desc = $seo['short_desc'];
        
        $cursor->name_s = Str::slug($cursor->name_en);

        $tagTranslation = \App\Models\CursorTagTranslation::where('cursor_id', $cursor->id)
            ->where('lang', app()->getLocale())
            ->first();

        if ($tagTranslation) {
            $cursor->tags = explode(' ', $tagTranslation->tags, );
        }


        // Вибираємо ID для "вікна"
        $allIds = (clone $baseQuery)->orderBy('id')->pluck('id')->toArray();
        $currentIndex = array_search($cursor->id, $allIds);

        // 2 попередніх, поточний, 2 наступних
        $windowIds = [];
        for ($i = -2; $i <= 2; $i++) {
            $idIndex = $currentIndex + $i;
            if ($idIndex >= 0 && $idIndex < count($allIds)) {
                $windowIds[] = $allIds[$idIndex];
            }
        }

        // Вибираємо курсори в правильному порядку
        $cursors = (clone $baseQuery)->whereIn('id', $windowIds)->get()->keyBy('id');

        $window = \Illuminate\Database\Eloquent\Collection::make(
            array_map(fn($id) => $cursors[$id] ?? null, $windowIds)
        )->filter();

        $window->load('currentTranslation');

        foreach ($window as $c) {
            $c->collection = $collections->first(fn($col) => $col->id === $c->cat);
            $seo = CursorPresenter::seo($c);
            $c->detailsSlug = $seo['detailsSlug'];
            $c->collectionSlug = $seo['collectionSlug'];
            $c->c_file = $seo['c_file'];
            $c->p_file = $seo['p_file'];
            $c->name_s = slugify($c->name_en);
            $c->cursor_slug = $seo['cursorTrans'];
        }

        $centralIndex = $window->search(fn($c) => $c && $c->id === $cursor->id);        

        // 3 випадкові інші колекції
        $random = $collections->where('id', '!=', $cursor->cat)->random(3);
        foreach ($random as $col) {
            $seo = CollectionPresenter::seo($col);
            $col->slug = $seo['slug'];
            $col->url = $seo['url'];
            $col->img = $seo['img'];
        }

        // Курсори цієї колекції
        $category_cursors = (clone $baseQuery)
            ->where('cat', $cursor->cat)
            ->orderBy('id')
            ->paginate(12);

        foreach ($category_cursors as $catCursor) {
            $catCursor->currentTranslation;
            $catCursor->collection = $collections->first(fn($col) => $col->id === $catCursor->cat);
            $seo = CursorPresenter::seo($catCursor);
            $catCursor->detailsSlug = $seo['detailsSlug'];
            $catCursor->c_file = $seo['c_file'];
            $catCursor->p_file = $seo['p_file'];
            $catCursor->name_s = slugify($catCursor->name_en);
            $catCursor->details_url = route('collection.cursor.details', [
                'cat' => $catCursor->cat,
                'collection_slug' => $seo['catTrans'],
                'id' => $catCursor->id,
                'cursor_slug' => $seo['cursorTrans'],
            ]);             
        }

        // Переходимо до шаблону, передаємо central, сусідів ліво/право
        return response()
            ->view('details', [
                'all_cursors' => $window,
                'cursor' => $cursor,
                'current_id' => $cursor->id,
                'id_prev' => $window->get($centralIndex - 1) ? [$window->get($centralIndex - 1)->id, $window->get($centralIndex - 1)->name_s] : null,
                'id_next' => $window->get($centralIndex + 1) ? [$window->get($centralIndex + 1)->id, $window->get($centralIndex + 1)->name_s] : null,
                'category_cursors' => $category_cursors,
                'random' => $random,
                'translations' => $translations,
                'translationsCat' => $translationsCat,
                'collection_base_name' => $catCursor->collection->base_name_en,
                'collection_id' => $r->cat
                // інші змінні
            ])
            ->header('Cache-Tag', 'details');
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

}
