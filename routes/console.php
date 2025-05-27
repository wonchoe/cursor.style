<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
Schedule::command('custom:ChromStats')->hourly();
Schedule::command('custom:GetGoogleAnalyticsData')->everyMinute();

// Create translation
Schedule::command('custom:translate-collections')->everyFiveMinutes();
Schedule::command('custom:TranslateCursor')->everyFiveMinutes();

// Create tags for SEO
Schedule::command('custom:tagsCreate')->everyFiveMinutes();

// Add to search
Schedule::command('custom:meilisearchAddCursors')->everyFiveMinutes();

Schedule::command('custom:GetCursorClickStats')->everyFiveMinutes();

// SEO chatGPT
Schedule::command('custom:seoSiscovery')->everyFiveMinutes();
Schedule::command('custom:seoBatchPrepareSend')->everyFiveMinutes();
Schedule::command('custom:seoBatchFetchSave')->everyFiveMinutes();
