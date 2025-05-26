<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
Schedule::command('custom:ChromStats')->hourly();
Schedule::command('custom:GetGoogleAnalyticsData')->hourly();

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

