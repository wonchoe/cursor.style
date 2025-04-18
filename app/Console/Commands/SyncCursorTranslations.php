<?php
// app/Console/Commands/SyncCursorTranslations.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\CursorTranslation;

class SyncCursorTranslations extends Command
{
    protected $signature = 'sync:cursor-translations';
    protected $description = 'Sync cursor names from lang files into DB';

    public function handle()
    {
        $langDirs = File::directories(resource_path('lang'));

        foreach ($langDirs as $langDir) {
            $locale = basename($langDir);
            $cursorFile = $langDir . '/cursors.php';

            if (!File::exists($cursorFile)) continue;

            $translations = File::getRequire($cursorFile);
            $count = 0;

            foreach ($translations as $key => $value) {
                if (!str_starts_with($key, 'c_')) continue;

                $cursorId = (int) str_replace('c_', '', $key);
                CursorTranslation::updateOrCreate(
                    ['lang' => $locale, 'cursor_id' => $cursorId],
                    ['name' => $value]
                );
                $count++;
            }

            $this->info("[$locale] Synced $count translations.");
        }

        $this->info('✅ All translations synced!');
    }
}
