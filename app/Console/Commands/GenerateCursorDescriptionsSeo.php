<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Http;

class GenerateCursorDescriptionsSeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:generate-all-seo-descriptions';
    protected $description = 'Generate JSONL files with SEO prompts for all cursor categories';

    public function handle()
    {
        $categoryId = '1';
       // $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
       $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');

        $category = DB::table('categories')->where('id', $categoryId)->first();
        if (!$category) {
            $this->error("Категорія не знайдена.");
            return 1;
        }

        $cursors = DB::table('cursors')
            ->where('cat', $categoryId)
            ->get(['id', 'name', 'name_en']);

        if ($cursors->isEmpty()) {
            $this->warn("Курсорів не знайдено для цієї категорії.");
            return 0;
        }

        $filename = storage_path("app/cursors_seo_{$categoryId}.jsonl");
        $file = fopen($filename, 'w');

        foreach ($cursors as $cursor) {
            $prompt = "Ти професійний SEO-копірайтер. Напиши унікальний SEO-опис курсора для браузера Google Chrome. Назва курсора: '{$cursor->name}'. Категорія: '{$category->base_name}'. Опиши переваги курсора, додай LSI-синоніми для тематики. Згадай, що це набір для розширення в браузері. Уникай шаблонності та повторів.";

            $line = [
                'custom_id' => "cursor_{$cursor->id}",
                'method' => 'POST',
                'url' => '/v1/chat/completions',
                'body' => [
                    'model' => 'gpt-4.1-mini-2025-04-14',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Ти SEO-копірайтер, який пише описи для сайту курсорів.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 120
                ]
            ];
            fwrite($file, json_encode($line, JSON_UNESCAPED_UNICODE) . "\n");
        }
        fclose($file);
        $this->info("Файл згенеровано: {$filename}");

        // Відправляємо файл в OpenAI (files API)
        $response = Http::withToken($apiKey)
            ->attach('file', file_get_contents($filename), basename($filename))
            ->asMultipart()
            ->post('https://api.openai.com/v1/files', [
                ['name' => 'purpose', 'contents' => 'batch'],
            ]);
        if (!$response->ok()) {
            $this->error("Помилка при завантаженні файлу: " . $response->body());
            return 2;
        }
        $fileId = $response->json('id');
        $this->info("Файл завантажено, file_id: $fileId");

        // Створюємо batch
        $batchResponse = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/batches', [
                'input_file_id' => $fileId,
                'endpoint' => '/v1/chat/completions',
                'completion_window' => '24h'
            ]);
        if (!$batchResponse->ok()) {
            $this->error("Помилка при створенні batch: " . $batchResponse->body());
            return 3;
        }
        $batchId = $batchResponse->json('id');
        $this->info("Batch створено, batch_id: $batchId");

        $this->line("Готово! Можеш перевіряти статус batch:");
        $this->line("curl https://api.openai.com/v1/batches/$batchId -H \"Authorization: Bearer $apiKey\"");
        return 0;
    }
}


