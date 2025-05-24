<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Cursors;
use App\Models\SeoCursorText;
use App\Jobs\SeoBatchPrepareAndSendJob;
use Illuminate\Support\Facades\Log;

class SeoDiscoveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $languages = [ 'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh' ];



    public function handle()
    {
        $batchSize = 100;
        $insertBatchSize = 100; // Окремо для інсерта
        Log::channel('seojobs')->info('Запущено SeoDiscoveryJob', ['time' => now()]);

        Cursors::chunk($batchSize, function ($cursors) use ($insertBatchSize) {
            $cursorIds = $cursors->pluck('id')->all();

            $existing = SeoCursorText::whereIn('cursor_id', $cursorIds)
                ->whereIn('lang', $this->languages)
                ->get(['cursor_id', 'lang'])
                ->map(fn($item) => $item->cursor_id . '_' . $item->lang)
                ->flip()
                ->all();

            $toInsert = [];

            foreach ($cursors as $cursor) {
                foreach ($this->languages as $lang) {
                    $key = $cursor->id . '_' . $lang;
                    if (!isset($existing[$key])) {
                        $toInsert[] = [
                            'cursor_id' => $cursor->id,
                            'lang' => $lang,
                            'status' => 'new',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            dump('toInsert count:', count($toInsert));

            // Інсертимо пачками
            foreach (array_chunk($toInsert, $insertBatchSize) as $chunk) {
                SeoCursorText::insert($chunk);
            }
        });

        echo "DONE!\n";
        SeoBatchPrepareAndSendJob::dispatch();
    }

}
