<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\Cursors;
use App\Models\Collection;

class GenerateMultiSitemap extends Command
{
    protected $signature = 'generate:multisitemap {--force}';
    protected $description = 'Генерує мультімовний sitemap.xml для курсорів і колекцій з SEO URL';

    protected $languages = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    // Статичні сторінки (def. url => priority)
    protected $staticPages = [
        '' => 1.00,
        'howto' => 0.80,
        'collections' => 0.80,
        'contact' => 0.80,
        'popular' => 0.80,
        'terms' => 0.80,
        'privacy' => 0.80,
        'cookie-policy' => 0.80,
    ];

    // Слово "cursor" для різних мов
    protected function cursorWordList()
    {
        return [
            'en' => 'cursor', 'am' => 'አውታረ አይነት', 'ar' => 'مؤشر', 'bg' => 'курсор', 'bn' => 'কার্সর',
            'ca' => 'cursor', 'cs' => 'kurzor', 'da' => 'markør', 'de' => 'zeiger', 'el' => 'δείκτης',
            'es' => 'cursor', 'et' => 'kursor', 'fa' => 'اشاره‌گر', 'fi' => 'osoitin', 'fil' => 'kurso',
            'fr' => 'curseur', 'gu' => 'કર્સર', 'he' => 'סמן', 'hi' => 'कर्सर', 'hr' => 'kursor',
            'hu' => 'kurzor', 'id' => 'kursor', 'it' => 'cursore', 'ja' => 'カーソル', 'kn' => 'ಕರ್ಸರ್',
            'ko' => '커서', 'lt' => 'žymeklis', 'lv' => 'kursors', 'ml' => 'കഴ്സർ', 'mr' => 'कर्सर',
            'ms' => 'kursor', 'nl' => 'cursor', 'no' => 'markør', 'pl' => 'kursor', 'pt' => 'cursor',
            'ro' => 'cursor', 'ru' => 'курсор', 'sk' => 'kurzor', 'sl' => 'kazalec', 'sr' => 'курсор',
            'sv' => 'markör', 'sw' => 'kiashiria', 'ta' => 'சுட்டி', 'te' => 'కర్సర్', 'th' => 'เคอร์เซอร์',
            'tr' => 'imleç', 'uk' => 'курсор', 'vi' => 'con trỏ', 'zh' => '光标',
        ];
    }

    public function handle()
    {
        $baseDir = public_path('sitemaps');
        $force = $this->option('force');
        $cursorWords = $this->cursorWordList();

        $this->line("=== Генерація sitemap для мультимовного сайту (SEO-friendly) ===");

        $this->generateSitemapForLang('en', $baseDir, $force, true, $cursorWords);
        
        foreach ($this->languages as $lang) {
            
            if ($lang === 'en') continue;
            $this->generateSitemapForLang($lang, $baseDir, $force, false, $cursorWords);                    
        }

        $this->info('=== Всі sitemap згенеровані! ===');
    }

    protected function generateSitemapForLang($lang, $baseDir, $force = false, $isDefault = false, $cursorWords = [])
    {
        $sitemapDir = $isDefault ? "$baseDir/default" : "$baseDir/$lang";
        if (!File::exists($sitemapDir)) {
            File::makeDirectory($sitemapDir, 0755, true);
            $this->line("Створено папку: $sitemapDir");
        }

        $sitemapPath = "$sitemapDir/sitemap.xml";
        if (File::exists($sitemapPath) && !$force) {
            $this->warn("Sitemap вже існує для [$lang], скіпаю (додай --force щоб перегенерувати)");
            return;
        }

        $seoTitle = $isDefault
            ? 'Sitemap for cursor.style'
            : "Sitemap for $lang.cursor.style";

        $domain = $isDefault ? 'https://cursor.style' : "https://{$lang}.cursor.style";
        $now = Carbon::now()->toIso8601String();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<!-- $seoTitle -->
XML;

        $addedUrls = [];

        // Статичні сторінки
        $staticCount = 0;
        foreach ($this->staticPages as $slug => $priority) {
            $loc = $domain . ($slug ? '/' . $slug : '');
            $xml .= $this->makeUrlBlock($loc, $now, $priority);
            $addedUrls[] = $loc;
            $staticCount++;
        }
        $this->line("[$lang] Додаємо статичних сторінок: $staticCount");

        // Готуємо всі переклади для курсорів і колекцій ОДНИМ ЗАПИТОМ для прискорення!
        $collectionTranslations = \App\Models\CollectionTranslation::where('lang', $lang)
            ->get()
            ->keyBy('collection_id'); // collection_id => translation
        $cursorTranslations = \App\Models\CursorTranslation::where('lang', $lang)
            ->get()
            ->keyBy('cursor_id'); // cursor_id => translation

        // Колекції
        $collections = Collection::select(['id', 'base_name_en', 'updated_at'])
            ->with('translations')
            ->get();

        $collectionCount = 0;
        foreach ($collections as $collection) {
            $translation = $collectionTranslations->get($collection->id);
            $slug = $translation && $translation->name ? slugify($translation->name) : slugify($collection->base_name_en);
            $loc = "{$domain}/collections/{$collection->id}-{$slug}";
           
            if (!in_array($loc, $addedUrls)) {
                $xml .= $this->makeUrlBlock($loc, $now, 0.8);
                $addedUrls[] = $loc;
                $collectionCount++;
            }
        }
        $this->line("[$lang] Додаємо колекцій: $collectionCount");


        $cursorCount = 0;
        $failCount = 0;

        // chunk(500) можна змінити, якщо треба більшу/меншу партію
        Cursors::select(['id', 'cat', 'name_en', 'updated_at'])
            ->with('translations')
            ->chunk(500, function ($cursors) use (
                &$cursorCount, &$failCount, $collections, $collectionTranslations, $cursorTranslations, $cursorWords, $lang, $domain, $now, &$xml, &$addedUrls
            ) {
                foreach ($cursors as $cursor) {
                    try {
                        // Колекція для курсора
                        $collection = $collections->where('id', $cursor->cat)->first();
                        if (!$collection) {
                            echo "[$lang] Курсор id={$cursor->id} не має валідної колекції (cat={$cursor->cat})\n";
                            $failCount++;
                            continue;
                        }
                        $collectionTranslation = $collectionTranslations->get($collection->id);
                        $collectionSlug = $collectionTranslation && $collectionTranslation->name
                            ? slugify($collectionTranslation->name)
                            : slugify($collection->base_name_en);

                        // Переклад курсора
                        $cursorTranslation = $cursorTranslations->get($cursor->id);
                        $cursorSlug = $cursorTranslation && $cursorTranslation->name
                            ? slugify($cursorTranslation->name)
                            : slugify($cursor->name_en);

                        $cursorWord = $cursorWords[$lang] ?? 'cursor';

                        $loc = "{$domain}/collections/{$collection->id}-{$collectionSlug}/{$cursor->id}-{$cursorSlug}-{$cursorWord}";

                        if (!in_array($loc, $addedUrls)) {
                            $xml .= $this->makeUrlBlock($loc, $now, 0.8);
                            $addedUrls[] = $loc;
                            $cursorCount++;
                        }
                    } catch (\Throwable $e) {
                        echo "[$lang] ERROR for cursor id={$cursor->id}: ".$e->getMessage()."\n";
                        $failCount++;
                        continue;
                    }
                }
            });

        $this->line("[$lang] Додаємо курсорів: $cursorCount");
        if ($failCount > 0) {
            $this->warn("[$lang] Пропущено курсорів через помилки: $failCount");
        }

        $total = $staticCount + $collectionCount + $cursorCount;
        $this->info("[$lang] Записано $total посилань у $sitemapPath");

        $xml .= "\n</urlset>";
        File::put($sitemapPath, $xml);

    }

    protected function makeUrlBlock($loc, $lastmod, $priority = 0.8)
    {
        $loc = htmlspecialchars($loc);
        return <<<XML

<url>
  <loc>{$loc}</loc>
  <lastmod>{$lastmod}</lastmod>
  <priority>{$priority}</priority>
</url>
XML;
    }
}