<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CursorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorController;
use App\Http\Controllers\TraficController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\GA4AnalyticsController;
use App\Http\Controllers\SitemapController;

use Illuminate\Http\Request;


Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/admin/analytics/installs', [GA4AnalyticsController::class, 'getInstallCount']);

Route::domain('reports.cursor.style')->group(function () {
    Route::get('/', [ReportsController::class, 'index']); // Assuming you have an index method
});

Route::view('/empty', 'other.empty');

Route::prefix('/collections')->group(function() {
    Route::get('/', [IndexController::class,'showAllCat']);
    Route::get('/{alt_name}/', [IndexController::class,'showCat']);
});

Route::get('/mycollection', [IndexController::class, 'mycollection']);

Route::get('/details/{id}-{name}', [IndexController::class,'showCursorPreview']);


Route::get('/{type}/{id}-{cursor}.svg', [ImageController::class, 'getSvg'])->whereIn('type', ['cursors', 'pointers'])->name('svg');

Route::get('/{type}/{id}-{cursor}', function(Request $r){
    return redirect('/'.$r->type.'/'.$r->id.'-'.$r->cursor.'.svg');
})->whereIn('type', ['cursors', 'pointers']);

Route::get('dashboard', [CustomAuthController::class, 'dashboard']);
Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');



Route::get('/js/lang.js', [IndexController::class, 'showJsLang'])->name('assets.lang');
Route::post('/update/set', [IndexController::class, 'update']);
Route::view('/youtube-skins-themes', 'other.youtube');
Route::view('/v3_update', 'v3update');
Route::view('/contact', 'contact');
Route::post('/contact', function () {
    return view('contact', ['success' => 'true']);
});
Route::get('/feedback', [IndexController::class, 'showFeedback']);
Route::post('/feedback', [IndexController::class, 'sendEmailFeedBack']);
Route::view('/howto', 'howto');

Route::view('/terms', 'other.terms');
Route::view('/privacy', 'other.privacy');
Route::view('/cookie-policy', 'other.cookiepolicy');

Route::get('/success', [IndexController::class, 'successInstall']);
Route::post('/success', [IndexController::class, 'successInstall']);

Route::get('/{type}/{category}/{name}', [ImageController::class, 'show'])->where('type', '^(c|p)(-)([0-9]+)?');

Route::get('/js/lang.js', [IndexController::class, 'showJsLang'])->name('assets.lang');

Route::get('/collection/{name}.png', [ImageController::class, 'showCollection']);

Route::get('/animated', [CursorController::class, 'getAllAnimated']);
Route::post('/settop', [IndexController::class, 'setTopCursor']);
Route::get('/getUpdates', [CursorController::class, 'getCode']);


Route::prefix('/admin')->group(function() {
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('auth');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('auth');
    Route::get('/cursors/create', [CursorController::class, 'create'])->name('cursors.create')->middleware('auth');
    Route::post('/cursors', [CursorController::class, 'store'])->name('cursors.store')->middleware('auth');

    // Route::get('/', [DashboardController::class,'show'])->middleware('auth');
    // Route::get('/setCursorLang', [CursorController::class, 'setCursorLang'])->middleware('auth');
    // Route::get('/getStatDB', [CursorController::class, 'uploadDb'])->middleware('auth');
    // Route::get('/getAniDB', [CursorController::class, 'uploadAniDb'])->middleware('auth');
    // Route::get('/editor', [EditorController::class, 'show'])->middleware('auth');
    // Route::post('/editor/code/get', [EditorController::class, 'getCode'])->middleware('auth');
    // Route::post('/editor/code/set', [EditorController::class, 'setCode'])->middleware('auth');
    // Route::get('/install', [TraficController::class, 'show'])->middleware('auth');
    // Route::post('/install', [TraficController::class, 'archive'])->middleware('auth');
    // Route::get('/install/archived', [TraficController::class, 'showArchived'])->middleware('auth');
    // Route::get('/cursors', [CursorController::class, 'show'])->middleware('auth');
    // Route::get('/uninstalled', [DashboardController::class,'showUninstalled'])->middleware('auth');
    // Route::post('/cursors/categories/save', [CursorController::class, 'saveCat'])->middleware('auth');
    // Route::get('/cursors/categories/get', [CursorController::class, 'getCat'])->middleware('auth');
    // Route::get('/cursors/getAll', [CursorController::class, 'getAll'])->middleware('auth');
    // Route::get('/getAll', [CursorController::class, 'getAll']);
    // Route::post('/cursor/upload', [CursorController::class, 'upload'])->middleware('auth');
    // Route::post('/cursors/delete', [CursorController::class, 'delete'])->middleware('auth');
    // Route::get('/reinit', [CursorController::class, 'reInitStatic'])->middleware('auth');
    // Route::post('/reinit/update', [CursorController::class, 'updateStatic'])->middleware('auth');
    // Route::get('/reinitani', [CursorController::class, 'reInitAni'])->middleware('auth');
    // Route::post('/reinit/updateani', [CursorController::class, 'updateAni'])->middleware('auth');
    // Route::view('/animated', 'admin.animated')->middleware('auth');
    // Route::get('/animated/getAll', [CursorController::class, 'getAllAnimated'])->middleware('auth');
    // Route::post('/animated/upload', [CursorController::class, 'uploadAnimated'])->middleware('auth');
    // Route::post('/animated/delete', [CursorController::class, 'deleteAnimated'])->middleware('auth');
});


Route::get('/{type?}', [IndexController::class, 'show']);

Route::middleware('debug')->group(function () {
	Route::get('/debug-path', [IndexController::class,'debugPath']);
});
