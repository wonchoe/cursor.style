<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class TranslateCursorNames extends Command
{
    protected $signature = 'translate:cursor-names';
    protected $description = 'Проставляє переклад cursor name у поле translation в cursor_tag_translations';

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi',
        'fil', 'fr', 'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv',
        'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw',
        'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    protected function batchUpdateTranslation(array $updates): void
    {
        if (empty($updates)) return;
    
        $cases = [];
        $ids = [];
    
        foreach ($updates as $update) {
            $id = (int)$update['id'];
            $translation = addslashes($update['translation']);
            $cases[] = "WHEN {$id} THEN '{$translation}'";
            $ids[] = $id;
        }
    
        $caseString = implode("\n", $cases);
        $idList = implode(',', $ids);
    
        $sql = "
            UPDATE cursor_tag_translations
            SET translation = CASE id
                {$caseString}
            END
            WHERE id IN ({$idList});
        ";
    
        DB::statement($sql);
    }
    
    public function handle()
    {
        foreach ($this->languages as $lang) {
            App::setLocale($lang);
            $this->info("🌍 Мова: $lang");
    
            DB::table('cursor_tag_translations')
                ->where('lang', $lang)
                ->whereNull('translation')
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($lang) {
                    $updates = [];
    
                    foreach ($rows as $row) {
                        $key = "cursors.c_{$row->cursor_id}";
                        $translated = trans($key);
    
                        if ($translated !== $key) {
                            $updates[] = [
                                'id' => $row->id,
                                'translation' => $translated,
                            ];
                        }
                    }
    
                    $this->batchUpdateTranslation($updates);
                    $this->line("✅ Batch записано: " . count($updates));
                });
        }
    
        $this->info("🎉 Переклад завершено.");
    }
    
    
}
