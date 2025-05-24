<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
 

// Замість Schedule::job() -> Schedule::call() з dispatch-ом job'и

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
