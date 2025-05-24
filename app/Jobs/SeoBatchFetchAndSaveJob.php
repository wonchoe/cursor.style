<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\SeoCursorText;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SeoBatchFetchAndSaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro',
        'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    protected function cleanJsonBlock($content)
    {
        $content = trim($content);
        // Видалити початок ```json або ```
        $content = preg_replace('/^```json[\r\n]?|^```[\r\n]?/i', '', $content);
        // Видалити кінець ```
        $content = preg_replace('/```$/', '', $content);
        return trim($content);
    }    
    public function handle()
    {
        Log::channel('seojobs')->info('Запущено SeoBatchFetchAndSaveJob', ['time' => now()]);

        $pendingBatches = SeoCursorText::where('status', 'pending')
            ->whereNotNull('batch_id')
            ->distinct()
            ->pluck('batch_id')
            ->toArray();

        
        foreach ($pendingBatches as $batchId) {
            $resp = Http::withToken(env('OPENAI_API_KEY'))
                ->get("https://api.openai.com/v1/batches/{$batchId}");
            if (!$resp->successful()) continue;

            $batchInfo = $resp->json();
            $status = $batchInfo['status'] ?? '';

            // Якщо batch failed — всі курсори назад у new + чистимо batch_id
            if ($status === 'failed') {
                SeoCursorText::where('batch_id', $batchId)->update([
                    'status' => 'new',
                    'batch_id' => null,
                    'updated_at' => now(),
                ]);
                Log::channel('seojobs')->info("Batch {$batchId} failed. All its items reset to new.");
                continue;
            }

            // Якщо ще не completed — пропускаємо
            Log::channel('seojobs')->info("Batch {$batchId} status {$status} ");
            if ($status !== 'completed' && $status !== 'cancelled' && $status !== 'canceled') continue;

            $outputFileId = $batchInfo['output_file_id'] ?? null;
            if (!$outputFileId) continue;

            $fileResp = Http::withToken(env('OPENAI_API_KEY'))
                ->get("https://api.openai.com/v1/files/{$outputFileId}/content");

            $lines = explode("\n", $fileResp->body());
            Log::channel('seojobs')->info("Lines length ", ['Length' => count($lines)]);
            $seoUpdates = [];
            $erroredCursors = [];
            $errorLines = [];
            foreach ($lines as $line) {
                if (!$line) continue;
                $json = json_decode($line, true);
                $content = $json['response']['body']['choices'][0]['message']['content'] ?? null;
                if (!$content) {
                    Log::channel('seojobs')->info("No content ", ['Line' => $line]);
                    // Якщо контенту немає, статус -> new, batch_id -> null
                    $customId = $json['custom_id'] ?? null;
                    Log::channel('seojobs')->info('Error SeoBatchFetchAndSaveJob', ['time' => now(), 'line' => $line]);
                    if ($customId && preg_match('/cursor_(\d+)_(\w+)/', $customId, $m)) {
                        $cursorId = $m[1];
                        $lang = $m[2];
                        SeoCursorText::where('cursor_id', $cursorId)->where('lang', $lang)
                            ->update(['status' => 'new', 'batch_id' => null, 'updated_at' => now()]);
                    }
                    $errorLines[] = $line;
                    continue;
                }

                $cleanContent = $this->cleanJsonBlock($content);
                $seo = json_decode($cleanContent, true);
                if (
                    !$seo
                    || empty($seo['cursor_id'])
                    || empty($seo['lang'])
                    || !in_array($seo['lang'] ?? '', $this->languages)
                    || !isset($seo['title'], $seo['description'], $seo['page'])
                ) {
                    // Витягуємо через regex, якщо потрібно
                    $cursorId = $seo['cursor_id'] ?? null;
                    $lang = $seo['lang'] ?? null;

                    if (!$cursorId && $content) {
                        if (preg_match('/"cursor_id"\s*:\s*(\d+)/', $content, $m1)) {
                            $cursorId = $m1[1];
                        }
                    }
                    if (!$lang && $content) {
                        if (preg_match('/"lang"\s*:\s*"([a-z]{2,5})"/i', $content, $m2)) {
                            $lang = $m2[1];
                        }
                    }

                    Log::channel('seojobs')->error('SEO json_decode failed', [
                        'content' => $content,
                        'json_last_error' => json_last_error_msg(),
                        'cursor_id' => $cursorId,
                        'lang' => $lang,
                    ]);
                    if ($cursorId && $lang) {
                        SeoCursorText::where('cursor_id', $cursorId)->where('lang', $lang)
                            ->update(['status' => 'new', 'batch_id' => null, 'updated_at' => now()]);
                    }
                    $errorLines[] = $line;
                    continue;
                }

                $seoUpdates[] = [
                    'cursor_id' => $seo['cursor_id'],
                    'lang' => $seo['lang'],
                    'seo_title' => $seo['title'],
                    'seo_description' => $seo['description'],
                    'seo_page' => $seo['page'],
                    'status' => 'done',
                    'updated_at' => now(),
                ];
            }


            // Якщо є помилки по курсорах — повертаємо їх у new, batch_id null
            if ($erroredCursors) {
                foreach (array_chunk($erroredCursors, 100) as $chunk) {
                    $ids = collect($chunk)->pluck('cursor_id')->all();
                    $langs = collect($chunk)->pluck('lang')->all();
                    SeoCursorText::whereIn('cursor_id', $ids)
                        ->whereIn('lang', $langs)
                        ->update(['status' => 'new', 'batch_id' => null, 'updated_at' => now()]);
                }
                // Записуємо помилки у лог-файл
                $errorLogPath = storage_path('logs/seo_batch_error_' . $batchId . '.log');
                file_put_contents($errorLogPath, implode("\n", $errorLines), FILE_APPEND);
            }

            // Апдейтимо валідні
            foreach (array_chunk($seoUpdates, 500) as $chunk) {
                SeoCursorText::upsert(
                    $chunk,
                    ['cursor_id', 'lang'],
                    ['seo_title', 'seo_description', 'seo_page', 'status', 'updated_at']
                );
            }

            // Після обробки batch_id прибираємо у всіх, де status != pending/new (тобто залишаємо для косячних)
            SeoCursorText::where('batch_id', $batchId)
                ->where('status', 'done')
                ->update(['batch_id' => null, 'updated_at' => now()]);
        }
    }
}
