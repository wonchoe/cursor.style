<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\CollectionTranslation;
use App\Models\categories;

class SyncCollectionsTranslations extends Command
{
    protected $signature = 'custom:syncCollectionsTranslation';
    protected $description = 'Sync collections names from lang files into DB';

    public function handle()
    {
        $langDirs = File::directories(resource_path('lang'));

        foreach ($langDirs as $langDir) {
            $locale = basename($langDir);
            $collectionsFile = $langDir . '/collections.php';

            if (!File::exists($collectionsFile)) continue;

            $translations = File::getRequire($collectionsFile);
            $count = 0;

            foreach (categories::all() as $cat) {
                $alt = $cat->alt_name;

                $keyName = $alt;
                $keyShort = "{$alt}_short_descr";
                $keyDesc = "{$alt}_descr";

                if (!isset($translations[$keyName])) continue;

                CollectionTranslation::updateOrCreate(
                    ['lang' => $locale, 'collection_id' => $cat->id],
                    [
                        'name' => $translations[$keyName],
                        'short_desc' => $translations[$keyShort] ?? '',
                        'desc' => $translations[$keyDesc] ?? '',
                    ]
                );
                $count++;
            }

            $this->info("[$locale] ✅ Synced $count collections.");
        }

        $this->info('🎉 All collection translations synced!');
    }
}
