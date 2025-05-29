<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Collection;
use App\Models\Cursors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Support\CollectionPresenter;
use App\Support\CursorPresenter;
use \App\Models\CollectionTranslation;

class CollectionsController extends Controller
{


    public function myCollection()
    {
        $collections = Collection::with('currentTranslation')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($collections as $collection) {
            $seo = CollectionPresenter::seo($collection);
            $collection->slug = $seo['slug'];
            $collection->url = $seo['url'];
            $collection->img = $seo['img'];
            $collection->trans = $seo['trans'];
        }

        return response()
            ->view('mycollection', ['mycollection' => $collections])
            ->header('Cache-Tag', 'collections');
    }


    public function index()
    {
        $collections = Collection::with('currentTranslation')
            ->orderBy('id', 'desc')
            ->paginate(20);

        foreach ($collections as $collection) {
            $seo = CollectionPresenter::seo($collection);
            $collection->slug = $seo['slug'];
            $collection->url = $seo['url'];
            $collection->img = $seo['img'];
            $collection->alt = $seo['alt'];
            $collection->details_url = route('collection.show', [
                'id' => $collection->id,
                'slug' => $seo['trans'],
            ]);                  
        }    

        return response()
            ->view('collections', compact('collections'))
            ->header('Cache-Tag', 'collections');
    }

    public function showCollection($id, $alt_name, Request $request)
    {
        // Завантажуємо всі колекції з перекладом
        $collections = Collection::with('currentTranslation')->get();

        // Додаємо SEO-дані
        foreach ($collections as $col) {
            $seo = CollectionPresenter::seo($col);
            $col->slug = $seo['slug'];
            $col->url = $seo['url'];
            $col->img = $seo['img'];
            $col->trans = $seo['trans'];            
        }


        // Знаходимо головну колекцію по id
        $collection = $collections->firstWhere('id', $id);
        if (!$collection) {
            abort(404);
        }

        $translations = CollectionTranslation::where('collection_id', $id)
            ->pluck('name', 'lang')->toArray();        

        // Визначаємо ID для виключення курсора
        $excludeId = ($request->cookie('hide_item_2082') === 'true') ? 100000000 : 2082;

        // Завантажуємо курсори з цієї колекції
        $items = Cursors::whereDate('schedule', '<=', now())
            ->where('cat', $collection->id)
            ->where('id', '<>', $excludeId)
            ->orderBy('id')
            ->get();

        foreach ($items as $cursor) {
            $seo = CursorPresenter::seo($cursor);
            $cursor->slug_url_final = $seo['slug_url_final'];
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

        // Випадкові 3 колекції, крім основної
        $random = $collections->where('id', '!=', $id)->random(3);

        return response()->view('collection', [
            'alt_name' => $alt_name,
            'cursors' => $items,
            'collection' => $collection,
            'random' => $random,
            'translations' => $translations
        ])->header('Cache-Tag', 'collection');
    }


}