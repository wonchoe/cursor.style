<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
Schedule::command('custom:ChromStats')->hourly();
Schedule::command('custom:GetGoogleAnalyticsData')->everyMinute();

// Create translation
Schedule::command('custom:translate-collections')->everyTenMinutes();
Schedule::command('custom:TranslateCursor')->everyTenMinutes();

// Create tags for SEO
Schedule::command('custom:tagsCreate')->everyTenMinutes();

// Add to search
Schedule::command('custom:meilisearchAddCursors')->everyTenMinutes();



Schedule::call(function () {
    \Log::info('Schedule RUN ' . now());
})->everyThirtyMinutes();

Schedule::call(function () {
    (new SeoDiscoveryJob())->handle();
})->hourly();

Schedule::call(function () {
    (new SeoBatchPrepareAndSendJob())->handle();
})->everyFiveMinutes();

Schedule::call(function () {
    (new SeoBatchFetchAndSaveJob())->handle();
})->everyTenMinutes();

