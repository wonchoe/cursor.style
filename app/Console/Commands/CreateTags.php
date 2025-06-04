<?php
namespace App\Console\Commands;
use App\Models\Cursors;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class CreateTags extends Command
{
    protected $signature = 'custom:tagsCreate {--limit=50}';
    protected $description = 'Генерує англомовні теги для курсорів через OpenRouter API';
    protected $languages = [ 'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh' ];

    private $key;

    public function __construct()
    {
        parent::__construct();
        $this->key = config('services.openrouter.key');
    }


    private function requestTagsFromOpenRouter(array $items, string $lang, string $languageName): ?array
    {
        $prompt = "You are an assistant that generates relevant tags (1 to 10 short keywords or phrases) for UI items in a Chrome extension called \"Cursor Style\". Each item represents a unique mouse cursor inspired by games, cartoons, anime, or pop culture.
        
        Your task is to generate tags **in the following language**: \"$languageName\".
        
        Generate 3 to 8 relevant tags (short keywords or phrases) that best describe the theme, style, category, or origin of the cursor.
        
        Only include tags that are meaningful and contextually related. It's OK to include stylistic or descriptive words if they reasonably match the cursor's theme.
        
        Do not generate generic or unrelated words just to reach 8.
        
        ### Input format:
        You will receive a list of cursor objects in JSON format. Each object includes:
        - \"id\": the unique cursor ID (you must return this ID unchanged),
        - \"cursor\": the name of the cursor (in English),
        - \"cat\": the category the cursor belongs to.
        
        ### Output format:
        Respond ONLY with valid JSON. For each item, return:
        - \"id\": same ID from input,
        - \"lang\": \"$lang\"
        - \"tags\": a single string of 1 to 10 space-separated keywords translated into $languageName
        
        Example output:
        [
          { \"id\": 1, \"lang\": \"$lang\", \"tags\": \"герой бетмен дс темний\" },
          { \"id\": 2, \"lang\": \"$lang\", \"tags\": \"марио нінтендо водопровідник червоний\" }
        ]
        
        Important rules:
        - Generate the tags directly in $languageName
        - Return exactly \"$lang\" in the \"lang\" field
        - Do NOT include explanations, comments, or formatting like Markdown
        - Do NOT translate brand names or the name \"Cursor Style\"
        
        Now generate tags for the following input:
        
        " . json_encode($items, JSON_PRETTY_PRINT);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->key,
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
                return isset($item['id'], $item['tags'], $item['lang']) && is_string($item['tags']) && trim($item['tags']) !== '';
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
    $totalCursors = Cursors::count();
    $this->info("Загальна кількість курсорів: $totalCursors");
    $batchSize = 50;

    foreach ($this->languages as $lang) {
        $this->info("🌍 Мова: $lang");
        $offset = 0;

        while (true) {
            // 1. Отримуємо 50 курсорів
            $cursors = Cursors::with('Collection')
                ->orderBy('id')
                ->offset($offset)
                ->limit($batchSize)
                ->get();

            $this->info("Отримано курсорів: " . $cursors->count() . " зі зміщенням $offset");

            if ($cursors->isEmpty()) {
                $this->info("✅ Курсори закінчились для мови $lang");
                break;
            }

            $cursorIds = $cursors->pluck('id')->toArray();

            // 2. Вибираємо всі id для яких вже є теги
            $existingTagIds = DB::table('cursor_tag_translations')
                ->where('lang', $lang)
                ->whereIn('cursor_id', $cursorIds)
                ->pluck('cursor_id')
                ->toArray();

            // 3. Вибираємо англомовні теги для всіх курсорів цієї пачки (на всяк випадок, якщо мова не en)
            $enTagsMap = [];
            if ($lang !== 'en') {
                $enTagsMap = DB::table('cursor_tag_translations')
                    ->where('lang', 'en')
                    ->whereIn('cursor_id', $cursorIds)
                    ->pluck('tags', 'cursor_id')
                    ->toArray();
            }

            $batch = [];

            
            foreach ($cursors as $cursor) {
                if (in_array($cursor->id, $existingTagIds)) {
                    continue; // Пропускаємо, якщо вже є теги для цієї мови
                }

                if ($lang === 'en') {
                    $batch[] = [
                        'id' => $cursor->id,
                        'cursor' => $cursor->name_en,
                        'cat' => $cursor->collection->base_name_en ?? ''
                    ];
                } elseif (!empty($enTagsMap[$cursor->id])) {
                    $batch[] = [
                        'id' => $cursor->id,
                        'tags' => $enTagsMap[$cursor->id]
                    ];
                }
            }

            if (empty($batch)) {
                $offset += $batchSize;
                continue;
            }

            $this->info("➡ Відправка " . count($batch) . " курсорів для мови [$lang]");

            $result = $this->requestTagsFromOpenRouter($batch, $lang, $this->getLanguageName($lang));

            if (!$result) {
                $this->error("❌ Помилка запиту [$lang]");
                continue;
            }

            foreach ($result as $item) {
                if (!isset($item['id'], $item['tags']))
                    continue;

                DB::table('cursor_tag_translations')->updateOrInsert(
                    ['cursor_id' => $item['id'], 'lang' => $lang],
                    ['tags' => $item['tags'], 'updated_at' => now(), 'created_at' => now()]
                );
                $this->info("[$lang] ✔ ID {$item['id']} → {$item['tags']}");
            }

            $offset += $batchSize;
            sleep(1); // throttle
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

