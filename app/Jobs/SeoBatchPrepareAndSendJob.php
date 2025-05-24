<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SeoCursorText;
use App\Models\Cursors;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class SeoBatchPrepareAndSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::channel('seojobs')->info('Запущено SeoBatchPrepareAndSendJob', ['time' => now()]);
        $maxBatch = 3000;

        // 0. Чи є зараз batch у статусі 'pending'? Якщо є — нічого не робимо
        $pending = SeoCursorText::where('status', 'pending')->whereNotNull('batch_id')->exists();
        if ($pending) {
            echo "Поточний batch у статусі pending, чекаємо на його завершення!\n";
            return;
        }

        // 1. Вибірка всіх id, які мають статус "new"
        $allNewIds = SeoCursorText::where('status', 'new')->pluck('id')->all();
        $total = count($allNewIds);

        if ($total === 0) {
            echo "Нема нових записів для batch!\n";
            return;
        }

        echo "Всього нових записів: $total\n";
        Log::channel('seojobs')->info('Total SeoBatchPrepareAndSendJob', ['Total' => $total]);        

        // 2. Беремо тільки перші $maxBatch
        $chunkIds = array_slice($allNewIds, 0, $maxBatch);

        $batch = SeoCursorText::whereIn('id', $chunkIds)->get();
        
        if ($batch->isEmpty()) {
            echo "Жодного валідного курсора для batch batch empty!\n";
            Log::channel('seojobs')->info('Жодного валідного курсора для batch!');        
            return;
        }

       
        // Підтягуємо курсори разом із колекцією
        $cursors = Cursors::whereIn('id', $batch->pluck('cursor_id'))->get()->keyBy('id');

        $lines = [];


        foreach ($batch as $item) {
            $cursor = $cursors[$item->cursor_id] ?? null;

            if (!$cursor) continue;

            $collection = $cursor->Collection;
            $categoryName = $collection->base_name_en ?? '';
            if (empty($categoryName)) continue;

            $langName = $this->getLanguageName($item->lang);

            $lines[] = json_encode([
                'custom_id' => "cursor_{$item->cursor_id}_{$item->lang}",
                'method' => 'POST',
                'url' => '/v1/chat/completions',
                'body' => [
                    "model" => "gpt-4.1-mini-2025-04-14",
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an SEO copywriter for a Chrome extension site with a large catalog of custom cursors, grouped by categories."
                        ],
                        [
                            'role' => 'user',
                            'content' =>
                                "Create three fields for the cursor named \"{$cursor->name_en}\" in the category \"{$categoryName}\":\n
1. `title`: concise and catchy, up to 60 characters.\n
2. `description`: short meta description, up to 160 characters.\n
3. `page`: unique and detailed SEO text, 80-200 words (not just 2-3 sentences), focusing on the cursor, category, its use in the Chrome extension, and its benefits for users.\n
All text must be written in {$langName}.\n
IMPORTANT: In the `lang` field, return exactly this code: \"{$item->lang}\". Do NOT translate or modify it.\n
Return result as a JSON: {\"cursor_id\": {$item->cursor_id}, \"lang\": \"{$item->lang}\", \"title\":\"...\", \"description\":\"...\", \"page\":\"...\"}"
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ],
            ], JSON_UNESCAPED_UNICODE);
        }

        if (empty($lines)) {
            echo "Жодного валідного курсора для batch 2!\n";
            Log::channel('seojobs')->info('Жодного валідного курсора для batch 2!');        
            return;
        }

        // 3. Записуємо batch-файл
        $dir = storage_path('app/seo_batches');
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = $dir . '/batch_' . now()->format('Ymd_His') . '.jsonl';
        file_put_contents($filename, implode("\n", $lines));

        // 4. Відправляємо файл у OpenAI
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->attach(
                    'file',
                    fopen($filename, 'r'),
                    basename($filename)
                )
                ->post('https://api.openai.com/v1/files', [
                    'purpose' => 'batch'
                ]);

            if (!$response->successful()) {
                echo "Помилка завантаження файлу у OpenAI: " . $response->body() . "\n";
                return;
            }

            $fileId = $response->json('id');

            // Тепер створюємо сам batch:
            $batchReq = Http::withToken(env('OPENAI_API_KEY'))
                ->post('https://api.openai.com/v1/batches', [
                    'input_file_id' => $fileId,
                    'endpoint' => '/v1/chat/completions',
                    'completion_window' => '24h',
                ]);

            if (!$batchReq->successful()) {
                echo "Помилка створення batch: " . $batchReq->body() . "\n";
                return;
            }

            $batchId = $batchReq->json('id');

            // 5. Оновлюємо статуси у БД:
            SeoCursorText::whereIn('id', $batch->pluck('id'))->update([
                'status' => 'pending',
                'batch_id' => $batchId,
                'updated_at' => now(),
            ]);

            echo "Batch успішно створено, batch_id: $batchId, записів: ".count($lines)."\n";
        } catch (\Exception $e) {
            echo "Виняток при відправці batch: " . $e->getMessage() . "\n";
            return;
        }

        echo "Batch відправлено!\n";
    }

    private function getLanguageName(string $lang): string
    {
        $map = [
            'en' => 'English',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'bg' => 'Bulgarian',
            'bn' => 'Bengali',
            'ca' => 'Catalan',
            'cs' => 'Czech',
            'da' => 'Danish',
            'de' => 'German',
            'el' => 'Greek',
            'es' => 'Spanish',
            'et' => 'Estonian',
            'fa' => 'Persian',
            'fi' => 'Finnish',
            'fil' => 'Filipino',
            'fr' => 'French',
            'gu' => 'Gujarati',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'hr' => 'Croatian',
            'hu' => 'Hungarian',
            'id' => 'Indonesian',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'lt' => 'Lithuanian',
            'lv' => 'Latvian',
            'ml' => 'Malayalam',
            'mr' => 'Marathi',
            'ms' => 'Malay',
            'nl' => 'Dutch',
            'no' => 'Norwegian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'sr' => 'Serbian',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'vi' => 'Vietnamese',
            'zh' => 'Chinese'
        ];

        return $map[$lang] ?? ucfirst($lang);
    }
}
