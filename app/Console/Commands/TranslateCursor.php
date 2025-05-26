<?php
namespace App\Console\Commands;
use App\Models\cursor;
use App\Models\CursorTranslation;
use App\Models\categories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class TranslateCursor extends Command
{
    protected $signature = 'custom:TranslateCursor {--limit=50}';
    protected $description = 'Генерує англомовні теги для курсорів через OpenRouter API';
    protected $languages = [ 'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh' ];

    private function requestTagsFromOpenRouter(array $items, string $lang, string $languageName): ?array
    {
        $prompt = "You are an assistant that translates UI item names into high-quality, natural-sounding equivalents for multilingual software.

        Translate the following list of cursor names from English into the target language: \"$languageName\".
        
        ### Input format:
        You will receive a list of cursor objects in JSON format. Each object includes:
        - \"id\": the unique cursor ID (you must return this ID unchanged),
        - \"name\": the original English name of the cursor
        
        ### Output format:
        Respond ONLY with valid JSON. For each item, return:
        - \"id\": same ID from input,
        - \"lang\": \"$lang\"
        - \"name\": translated cursor name in $languageName
        
        Example output:
        [
          { \"id\": 1, \"lang\": \"$lang\", \"name\": \"Темний Лицар\" },
          { \"id\": 2, \"lang\": \"$lang\", \"name\": \"Сердитий Маріо\" }
        ]
        
        Important rules:
        - Translate ONLY the name field
        - Return exactly \"$lang\" in the \"lang\" field
        - Do NOT include explanations, comments, or extra formatting like Markdown
        - Avoid literal translations for proper names unless they have a common local version
        
        Now translate the following:
        " . json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer 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',
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://cursor.style',
                'X-Title' => 'CursorStyleTagger'
            ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
                        'model' => 'google/gemini-2.0-flash-lite-001',
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ]
                    ]);

            if ($response->failed()) {
                $this->error('❌ OpenRouter error: ' . $response->body());
                return null;
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                $this->error('❌ Відповідь OpenRouter порожня або некоректна');
                return null;
            }

            $content = trim($content);
            if (str_starts_with($content, '```json')) {
                $content = preg_replace('/^```json\s*|\s*```$/', '', $content);
            }

            $parsed = json_decode($content, true);

            if (!is_array($parsed)) {
                $this->error('❌ JSON не розпізнано: ' . $content);
                return null;
            }

            // 🔎 Перевірка кожного елемента
            $valid = array_filter($parsed, function ($item) {
                return isset($item['id'], $item['name'], $item['lang']) && is_string($item['name']) && trim($item['name']) !== '';
            });

            $skipped = count($parsed) - count($valid);
            if ($skipped > 0) {
                $this->warn("⚠️ Пропущено $skipped елементів через невалідну структуру.");
            }

            return array_values($valid); // Перевпорядкування ключів
        } catch (\Exception $e) {
            $this->error('❌ Виняток при запиті: ' . $e->getMessage());
            return null;
        }

    }



    public function handle()
    {
        $this->info("🚀 Старт повного циклу: мова → курсори → запит...");
        $totalCursors = cursor::count();
        $this->info("Загальна кількість курсорів: $totalCursors");
        $batchSize = 50;

        foreach ($this->languages as $lang) {
            $this->info("🌍 Мова: $lang");

            $offset = 0;

            while (true) {
                // 1. Отримуємо 50 курсорів
                $cursors = cursor::with('categories')
                    ->orderBy('id')
                    ->offset($offset)
                    ->limit($batchSize)
                    ->get();
                $this->info("Отримано курсорів: " . $cursors->count() . " зі зміщенням $offset");

                if ($cursors->isEmpty()) {
                    $this->info("✅ Курсори закінчились для мови $lang");
                    break;
                }

                $batch = [];

                // 2. Перевіряємо кожен курсор на наявність перекладу
                foreach ($cursors as $cursor) {
                    $exists = DB::table('cursor_translations')
                        ->where('cursor_id', $cursor->id)
                        ->where('lang', $lang)
                        ->exists();

                        if (!$exists && $cursor->name_en) {
                            $batch[] = [
                                'id' => $cursor->id,
                                'name' => $cursor->name_en,
                            ];
                        }
                }

                // 3. Якщо нема чого обробляти — далі
                if (empty($batch)) {
                    $offset += $batchSize;
                    continue;
                }

                // 4. Відправка
                $this->info("➡ Відправка " . count($batch) . " курсорів для мови [$lang]");

                $result = $this->requestTagsFromOpenRouter($batch, $lang, $this->getLanguageName($lang));

                if (!$result) {
                    $this->error("❌ Помилка запиту [$lang]");
                    continue;
                }

                // 5. Збереження
                foreach ($result as $item) {
                    if (!isset($item['id'], $item['name']) || trim($item['name']) === '') {
                        $this->warn("⏭️ Пропущено ID {$item['id']} — відсутнє або порожнє ім’я.");
                        continue;
                    }
                
                    DB::table('cursor_translations')->updateOrInsert(
                        ['cursor_id' => $item['id'], 'lang' => $lang],
                        ['name' => $item['name'], 'updated_at' => now(), 'created_at' => now()]
                    );
                
                    $this->info("[$lang] ✔ ID {$item['id']} → {$item['name']}");
                }
                

                $offset += $batchSize;
//                sleep(1); // throttle
            }
        }

        $this->info("🎉 Завершено.");
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

