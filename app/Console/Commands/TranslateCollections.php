<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\CollectionTranslation;
use App\Models\Collection;

class TranslateCollections extends Command
{
    protected $signature = 'custom:translate-collections';
    protected $description = 'Translate collections (name, short_desc, desc) using AI and save to DB';

    protected $languages = [
        'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr',
        'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms',
        'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr',
        'uk', 'vi', 'zh'
    ];

    private $key = config('services.openrouter.key');

    public function handle()
    {
        foreach ($this->languages as $lang) {
            $this->info("\n🌍 Language: $lang");
            
            $batch = Collection::whereNotIn('id', function ($query) use ($lang) {
                    $query->select('collection_id')
                          ->from('collections_translations')
                          ->where('lang', $lang);
                })
                ->whereNotNull('base_name_en')
                ->whereNotNull('description')
                ->whereNotNull('short_descr')
                ->take(5)
                ->get();

            if ($batch->isEmpty()) {
                $this->info("✅ No untranslated collections left for $lang");
                continue;
            }

            $items = $batch->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->base_name_en,
                    'short_desc' => $cat->short_descr,
                    'desc' => $cat->description,
                ];
            })->toArray();

            $result = $this->requestCollectionTranslations($items, $lang);

            if (!$result) continue;

            foreach ($result as $item) {
                if (!isset($item['id'], $item['name'], $item['short_desc'], $item['desc'])) {
                    $this->warn("⏭️ Skipped invalid entry: " . json_encode($item));
                    continue;
                }

                CollectionTranslation::updateOrCreate(
                    ['collection_id' => $item['id'], 'lang' => $lang],
                    [
                        'name' => $item['name'],
                        'short_desc' => $item['short_desc'],
                        'desc' => $item['desc'],
                    ]
                );

                $this->info("[$lang] ✔ ID {$item['id']} → {$item['name']}");
            }

            sleep(2); // Throttle
        }

        $this->info("\n🎉 All collections processed!");
    }

    private function requestCollectionTranslations(array $items, string $lang): ?array
    {
        $languageName = $this->getLanguageName($lang);

        $prompt = "You are a localization assistant.\n\nTranslate the following list of collection descriptions from English into high-quality, natural-sounding {$languageName}.\n\n### Input format:\nEach object has:\n- \"id\": the collection ID\n- \"name\": collection name\n- \"short_desc\": short one-line description\n- \"desc\": longer detailed description\n\n### Output format:\n[\n  {\"id\": 1, \"lang\": \"{$lang}\", \"name\": \"...\", \"short_desc\": \"...\", \"desc\": \"...\" }\n]\n\nImportant rules:\n- Translate all fields into {$languageName}\n- Return exactly \"{$lang}\" in the \"lang\" field\n- Do not explain, annotate, or format in Markdown\n- Preserve proper names unless they have known local versions\n\nTranslate this:\n" . json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->key,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://cursor.style',
                'X-Title' => 'CursorStyleCollectionTranslator'
            ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemini-2.0-flash-lite-001',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->failed()) {
                $this->error("❌ OpenRouter error: " . $response->body());
                return null;
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? null;

            if (!$content) return null;

            if (str_starts_with(trim($content), '```json')) {
                $content = preg_replace('/^```json\s*|\s*```$/', '', trim($content));
            }

            $parsed = json_decode($content, true);
            return is_array($parsed) ? $parsed : null;

        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            return null;
        }
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
