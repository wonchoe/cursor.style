<?php
use App\Models\Cursors;
use App\Models\Collection;
use Illuminate\Support\Str;

/**
 * Генерує URL для курсора або колекції в заданій мові.
 * 
 * @param string $lang           - код мови ('en', 'uk' ...)
 * @param int|null $cursorId     - id курсора
 * @param int|null $collectionId - id колекції
 * @param string $linkType       - 'auto', 'cursor', 'collection'
 * @return string|false          - повний url, або false якщо не знайдено
 */
function getUrl($lang, $cursorId = null, $collectionId = null, $linkType = 'auto')
{   
    if (!$lang) $lang = app()->getLocale();
    // Валідація — має бути або курсор, або колекція
    if (!$cursorId && !$collectionId) {
        return false;
    }

    // Якщо треба курсор
    if (($linkType === 'auto' && $cursorId) || $linkType === 'cursor') {
        $cursor = Cursors::with(['translations', 'collection.translations'])->find($cursorId);
        if (!$cursor || !$cursor->collection) return false;

        // Колекція
        $collection = $cursor->collection;
        $catTranslations = $collection->translations->pluck('name', 'lang')->toArray();
        $catSlug = $catTranslations[$lang] ?? $collection->base_name_en ?? reset($catTranslations);
        $catSlug = slugify($catSlug);

        // Курсор
        $cursorTranslations = $cursor->translations->pluck('name', 'lang')->toArray();
        $cursorName = $cursorTranslations[$lang] ?? $cursor->name_en ?? reset($cursorTranslations);
        // Додаємо слово "cursor" в правильній мові (можна винести в окрему функцію)
        $cursorWord = cursorWordList()[$lang] ?? cursorWordList()['en'];
        $cursorSlug = slugify($cursorName . ' ' . $cursorWord);

        return "/collections/{$collection->id}-{$catSlug}/{$cursor->id}-{$cursorSlug}";
    }

    // Якщо треба колекцію
    if (($linkType === 'auto' && $collectionId) || $linkType === 'collection') {
        $collection = Collection::with('translations')->find($collectionId);
        if (!$collection) return false;

        $catTranslations = $collection->translations->pluck('name', 'lang')->toArray();
        $catSlug = $catTranslations[$lang] ?? $collection->base_name_en ?? reset($catTranslations);
        $catSlug = slugify($catSlug);

        return "/collections/{$collection->id}-{$catSlug}";
    }

    return false;
}


/**
 * Повертає масив перекладів слова "cursor"
 */
function cursorWordList()
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


if (!function_exists('build_version')) {
    function build_version(): string
    {
        $path = base_path('build.txt');
        if (file_exists($path)) {
            return '?v=' . trim(file_get_contents($path));
        }
        return '?v=dev';
    }
}


if (!function_exists('slugify')) {
    function slugify($string)
    {
        // якщо латиниця — Str::slug, інакше "помʼякшуємо" юнікод
        if (preg_match('/^[\p{Latin}0-9 ]+$/u', $string)) {
            return Str::slug($string);
        }
        $slug = preg_replace('/[^\p{L}\p{N}\s-]+/u', '', $string);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}


function renderHreflangLinksForCursor(int $cursorId, int $collectionId, array $translations, array $translationsCat, string $collection_base_name): string
{
    $cursorWord = [
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

    $langs = ['en','am','ar','bg','bn','ca','cs','da','de','el','es','et','fa','fi','fil','fr','gu','he','hi','hr','hu','id','it','ja','kn','ko','lt','lv','ml','mr','ms','nl','no','pl','pt','ro','ru','sk','sl','sr','sv','sw','ta','te','th','tr','uk','vi','zh'];

    // fallback for missing EN collection name
    if (!isset($translationsCat['en'])){
        $translationsCat['en'] = $collection_base_name;
    }

    // Додаємо "cursor" до кожного перекладу
    foreach ($translations as $lang => $name) {
        $cursorSuffix = $cursorWord[$lang] ?? $cursorWord['en'];
        $translations[$lang] = $name . ' ' . $cursorSuffix;
    }

    $fallbackCursorSlug = isset($translations['en']) ? slugify($translations['en']) : slugify(reset($translations));
    $fallbackCatSlug = isset($translationsCat['en']) ? slugify($translationsCat['en']) : slugify(reset($translationsCat));

    $html = '';
    $xDefaultUrl = '';

    foreach ($langs as $lang) {
        $cursorSlug = isset($translations[$lang]) ? slugify($translations[$lang]) : $fallbackCursorSlug;
        $catSlug = isset($translationsCat[$lang]) ? slugify($translationsCat[$lang]) : $fallbackCatSlug;
        $url = "https://{$lang}.cursor.style/collections/{$collectionId}-{$catSlug}/{$cursorId}-{$cursorSlug}";
        $html .= '    <link rel="alternate" hreflang="' . $lang . '" href="' . $url . '" />' . PHP_EOL;
        if ($lang === 'en') {
            $xDefaultUrl = $url;
        }
    }

    // x-default
    $html = '<link rel="alternate" hreflang="x-default" href="' . $xDefaultUrl . '" />' . PHP_EOL . $html;

    return $html;
}


if (!function_exists('renderHreflangLinksForCollection')) {
    /**
     * Генерує hreflang-лінки для колекції
     * @param int $collectionId
     * @param array $translations [ 'lang' => 'name', ... ]
     * @return string
     */
    function renderHreflangLinksForCollection(int $collectionId, array $translations, $basename): string
    {
        $langs = [
            'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
            'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
            'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
        ];

        if (empty($translations['en']) && !empty($basename)) {
            $translations['en'] = $basename;
        }

        // Фолбек на англійську або перший доступний переклад
        $fallbackSlug = isset($translations['en'])
            ? slugify($translations['en'])
            : slugify(reset($translations));

        $html = '';
        $xDefaultUrl = '';

        foreach ($langs as $lang) {
            $slug = isset($translations[$lang]) ? slugify($translations[$lang]) : $fallbackSlug;
            $url = "https://{$lang}.cursor.style/collections/{$collectionId}-{$slug}";
            $html .= '    <link rel="alternate" hreflang="' . $lang . '" href="' . $url . '" />' . PHP_EOL;
            if ($lang === 'en') {
                $xDefaultUrl = $url;
            }
        }

        // x-default, або фолбек на англійську
        $html = '    <link rel="alternate" hreflang="x-default" href="' . $xDefaultUrl . '" />' . PHP_EOL . $html;
        return $html;
    }
}

if (!function_exists('renderHreflangLinks')) {
    function renderHreflangLinks(): string
    {
        $langs = [
            'en','am','ar','bg','bn','ca','cs','da','de','el','es','et','fa','fi','fil','fr','gu','he','hi','hr','hu','id',
            'it','ja','kn','ko','lt','lv','ml','mr','ms','nl','no','pl','pt','ro','ru','sk','sl','sr','sv','sw','ta','te','th','tr','uk','vi','zh'
        ];

        $path = ltrim(request()->path(), '/');

        // Якщо це details/123-... або collections/123-... або collections/123-.../456-...
        if (
            preg_match('#^(details|collections)/\d+#', $path)     // старі сторінки
            || preg_match('#^collections/\d+-[^/]+/\d+-[^/]+#', $path) // нові SEO курсори
        ) {
            // Це сторінка курсора/колекції — hreflang для неї генерується окремим хелпером!
            return '';
        }

        // Для всіх інших — дефолтний hreflang
        $html = '<link rel="alternate" hreflang="x-default" href="https://cursor.style/' . $path . '" />' . PHP_EOL;

        foreach ($langs as $lang) {
            $html .= '<link rel="alternate" hreflang="' . $lang . '" href="https://' . $lang . '.cursor.style/' . $path . '" />' . PHP_EOL;
        }

        return $html;
    }
}



if (!function_exists('asset_base')) {
    function asset_base(): string
    {
        return rtrim(config('app.asset_cdn', 'https://cursor.style'), '/') . '/';
    }
}

if (!function_exists('asset_cdn')) {
    // Для іконок, картинок тощо (без версії)
    function asset_cdn(string $path): string
    {
        return asset_base() . ltrim($path, '/');
    }
}

if (!function_exists('asset_ver')) {
    // Для CSS/JS — додає білд-версію
    function asset_ver(string $path): string
    {
        return asset_base() . ltrim($path, '/') . build_version();
    }
}
