<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeoCursorText;
use App\Models\Cursors;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SeoBatchPrepareAndSendCommand extends Command
{
    protected $signature = 'custom:seoBatchPrepareSend';
    protected $description = 'Генерує і відправляє batch файл для OpenAI, оновлює статуси SeoCursorText';

    public function handle()
    {
        Log::channel('seojobs')->info('Запущено SeoBatchPrepareAndSendCommand', ['time' => now()]);
        $maxBatch = 3000;

        // 0. Чи є зараз batch у статусі 'pending'? Якщо є — нічого не робимо
        $pending = SeoCursorText::where('status', 'pending')->whereNotNull('batch_id')->exists();
        if ($pending) {
            $this->warn("Поточний batch у статусі pending, чекаємо на його завершення!");
            return;
        }

        // 1. Вибірка всіх id, які мають статус "new"
        $allNewIds = SeoCursorText::where('status', 'new')->pluck('id')->all();
        $total = count($allNewIds);

        if ($total === 0) {
            $this->info("Нема нових записів для batch!");
            return;
        }

        $this->info("Всього нових записів: $total");
        Log::channel('seojobs')->info('Total SeoBatchPrepareAndSendCommand', ['Total' => $total]);        

        // 2. Беремо тільки перші $maxBatch
        $chunkIds = array_slice($allNewIds, 0, $maxBatch);

        $batch = SeoCursorText::whereIn('id', $chunkIds)->get();
        
        if ($batch->isEmpty()) {
            $this->warn("Жодного валідного курсора для batch!");
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
            $this->warn("Жодного валідного курсора для batch 2!");
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
            $key = config('services.openai.key');
            $response = Http::withToken($key)
                ->attach(
                    'file',
                    fopen($filename, 'r'),
                    basename($filename)
                )
                ->post('https://api.openai.com/v1/files', [
                    'purpose' => 'batch'
                ]);

            if (!$response->successful()) {
                $this->error("Помилка завантаження файлу у OpenAI: " . $response->body());
                return;
            }

            $fileId = $response->json('id');

            // Тепер створюємо сам batch:
            $key = config('services.openai.key');
            $batchReq = Http::withToken($key)
                ->post('https://api.openai.com/v1/batches', [
                    'input_file_id' => $fileId,
                    'endpoint' => '/v1/chat/completions',
                    'completion_window' => '24h',
                ]);

            if (!$batchReq->successful()) {
                $this->error("Помилка створення batch: " . $batchReq->body());
                return;
            }

            $batchId = $batchReq->json('id');

            // 5. Оновлюємо статуси у БД:
            SeoCursorText::whereIn('id', $batch->pluck('id'))->update([
                'status' => 'pending',
                'batch_id' => $batchId,
                'updated_at' => now(),
            ]);

            $this->info("Batch успішно створено, batch_id: $batchId, записів: " . count($lines));
        } catch (\Exception $e) {
            $this->error("Виняток при відправці batch: " . $e->getMessage());
            return;
        }

        $this->info("Batch відправлено!");
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
