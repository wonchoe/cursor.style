<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
Schedule::command('custom:ChromStats')->hourly();
Schedule::command('custom:GetGoogleAnalyticsData')->everyFiveMinutes();

// Create translation
Schedule::command('custom:translate-collections')->everyThirtyMinutes();
Schedule::command('custom:TranslateCursor')->everyThirtyMinutes();

// Create tags for SEO
Schedule::command('custom:tagsCreate')->everyThirtyMinutes();

// Add to search
Schedule::command('custom:meilisearchAddCursors')->everyThirtyMinutes();

Schedule::command('custom:GetCursorClickStats')->everyThirtyMinutes();

// SEO chatGPT
Schedule::command('custom:seoSiscovery')->everyThirtyMinutes();
Schedule::command('custom:seoBatchPrepareSend')->everyThirtyMinutes();
Schedule::command('custom:seoBatchFetchSave')->everyThirtyMinutes();
