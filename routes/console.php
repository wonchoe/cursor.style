<?php

use App\Jobs\SeoDiscoveryJob;
use App\Jobs\SeoBatchPrepareAndSendJob;
use App\Jobs\SeoBatchFetchAndSaveJob;
use Illuminate\Support\Facades\Schedule;


Schedule::command('adsense:fetch')->everyFiveMinutes();
Schedule::command('custom:ChromStats')->hourly();

// Cursor installs/uninstalls
Schedule::command('custom:GetGoogleAnalyticsData')->everyFiveMinutes();
Schedule::command('custom:GetGoogleAnalyticsExtension')->everyFiveMinutes();

// Create translation
Schedule::command('custom:translate-collections')->everyThirtyMinutes();
Schedule::command('custom:TranslateCursor')->everyThirtyMinutes();

// Create tags for SEO
Schedule::command('custom:tagsCreate')->dailyAt('05:00');

// Add to search
Schedule::command('custom:meilisearchAddCursors --force')->dailyAt('05:10');

Schedule::command('custom:GetCursorClickStats')->everyFiveMinutes();
Schedule::command('custom:GetCursorClickStats --mode=yesterday')->daily();

// SEO chatGPT
Schedule::command('custom:seoSiscovery')->everyThirtyMinutes();
Schedule::command('custom:seoBatchPrepareSend')->everyThirtyMinutes();
Schedule::command('custom:seoBatchFetchSave')->everyThirtyMinutes();

// sitemap
Schedule::command('generate:multisitemap')->dailyAt('05:20');

// Submit sitemap to google
Schedule::command('custom:sitemap')->monthly();

