<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\GA4AnalyticsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ReportsChartController;
use App\Http\Controllers\Admin\LogViewerController;
use App\Http\Controllers\Admin\AdminCollectionsController;
use App\Http\Controllers\Admin\AdminCursorController;


// MAIN
Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/{type?}', [IndexController::class, 'index'])
    ->where('type', 'popular')
    ->name('popular');

// SEARCH
Route::post('/search', [SearchController::class, 'searchProxy']);
Route::get('/search/{q}', [SearchController::class, 'search']);

// COLLECTIONS
Route::prefix('/collections')->group(function() {
    Route::get('/', [CollectionsController::class, 'index']); 
    Route::get('/{id}-{slug}', [CollectionsController::class, 'showCollection'])
        ->where('id', '[0-9]+')
        ->name('collection.show');
});

// DETAILS
Route::get('/details/{id}-{name}', [IndexController::class,'detailsLegacy'])->name('cursor.details');
Route::get(
    '/collections/{cat}-{collection_slug}/{id}-{cursor_slug}',
    [IndexController::class, 'details']
)
->where(['cat' => '[0-9]+', 'id' => '[0-9]+'])
->name('collection.cursor.details');



// SITEMAP
Route::get('/sitemap.xml', [SitemapController::class, 'index']);    


// ADMIN
Route::prefix('/admin')->group(function() {
    Route::get('/analytics/installs', [GA4AnalyticsController::class, 'getInstallCount']);
});


// MY COLLECTION
Route::view('/mycollection', 'mycollection');
Route::get('/mycollection', [CollectionsController::class, 'myCollection']);

// CONTACT
Route::view('/contact', 'contact');
Route::post('/contact', function () {
    return view('contact', ['success' => 'true']);
});

// SUCCESS
Route::view('/success', 'other.success');

// HOW TO
Route::view('/howto', 'howto');

// OTHER
Route::view('/terms', 'other.terms');
Route::view('/privacy', 'other.privacy');
Route::view('/cookie-policy', 'other.cookiepolicy');

// FEEDBACK ON UNINSTALL
Route::view('/feedback', 'feedback');
Route::post('/feedback', [IndexController::class, 'sendEmailFeedBack']);


// IMAGES
// GROUP: TEMP + UNIVERSAL ROUTES FOR CURSOR IMAGES
Route::group([], function () {
    
    Route::get('/collections/{collection_slug}/thumbs/{filename}.png', [ImageController::class, 'serveThumbnail'])
        ->where('filename', '[0-9]+-[a-z0-9\\-]+-(cursor|pointer)');


    // 1. Старі URL: /cursors/1234-yellow-cursor(.svg|.png)
    Route::get('/{type}/{id}-{cursor}.{ext?}', function ($type, $id, $cursor, $ext = 'svg') {
        $slug = "{$id}-{$cursor}.{$ext}";
        return app(ImageController::class)->serveImage('legacy', $slug);
    })->whereIn('type', ['cursors', 'pointers'])->where('ext', 'svg|png');

    // IMAGE (svg/png)
    Route::get('/collections/{collection_slug}/{cursor_slug}.{ext?}', function ($collection_slug, $cursor_slug, $ext = 'svg') {
        $slug = "{$cursor_slug}.{$ext}";
        return app(ImageController::class)->serveImage($collection_slug, $slug);
    })->where('ext', 'svg|png');

    // 3. Старий формат з type=c-1234 або p-1234: /c-1234/category/name
    Route::get('/{type}/{category}/{name}', [ImageController::class, 'show'])
        ->where('type', '^(c|p)-[0-9]+$');

    Route::get('collections/{collection}/{file}.webp', [ImageController::class, 'serveWebp']);        
});



Route::middleware('auth')->group(function () {

    Route::domain('reports.cursor.style')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.dashboard');
    });


    Route::prefix('admin')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('admin.dashboard');

        // LOGS
        Route::get('/logs', [LogViewerController::class, 'index'])->name('admin.logs');
        Route::get('/logs/fetch', [LogViewerController::class, 'fetch'])->name('admin.logs.fetch');
        Route::post('/logs/clear', [LogViewerController::class, 'clear'])->name('admin.logs.clear');        

        // COLLECTIONS
        Route::get('/collections', [AdminCollectionsController::class, 'index'])->name('collections.index');
        Route::get('/collections/create', [AdminCollectionsController::class, 'create'])->name('collections.create');
        Route::post('/collections', [AdminCollectionsController::class, 'store'])->name('collections.store');
        Route::delete('/collections/delete/{id}', [AdminCollectionsController::class, 'destroy'])->name('collections.destroy');

       

        Route::get('/cursors', [AdminCursorController::class, 'index'])->name('cursors.index');
        Route::get('/cursors/create', [AdminCursorController::class, 'create'])->name('cursors.create');
        Route::post('/cursors', [AdminCursorController::class, 'store'])->name('cursors.store');
        Route::delete('/cursors/delete/{id}', [AdminCursorController::class, 'destroy'])->name('cursors.destroy');

        Route::get('/reinitDb', [AdminCursorController::class, 'reinitDb'])->name('cursors.reinitDb');        

    });

    Route::get('/report-chart-data', ReportsChartController::class)->name('report.chart');

});

require __DIR__.'/auth.php';
