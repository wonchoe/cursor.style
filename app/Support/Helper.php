<?php

if (!function_exists('build_version')) {
    function build_version(): string {
        $path = base_path('build.txt');
        if (file_exists($path)) {
            return '?v=' . trim(file_get_contents($path));
        }
        return '?v=dev';
    }
}

if (!function_exists('renderHreflangLinks')) {
    function renderHreflangLinks(): string {
        $langs = [
            'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
            'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
            'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
        ];

        $path = ltrim(request()->path(), '/');
        $html = '<link rel="alternate" hreflang="x-default" href="https://cursor.style/' . $path . '" />' . PHP_EOL;

        foreach ($langs as $lang) {
            $html .= '<link rel="alternate" hreflang="' . $lang . '" href="https://' . $lang . '.cursor.style/' . $path . '" />' . PHP_EOL;
        }

        return $html;
    }
}


if (!function_exists('asset_base')) {
    function asset_base(): string {
        return rtrim(config('app.asset_cdn', 'https://cursor.style'), '/') . '/';
    }
}

if (!function_exists('asset_cdn')) {
    // Для іконок, картинок тощо (без версії)
    function asset_cdn(string $path): string {
        return asset_base() . ltrim($path, '/');
    }
}

if (!function_exists('asset_ver')) {
    // Для CSS/JS — додає білд-версію
    function asset_ver(string $path): string {
        return asset_base() . ltrim($path, '/') . build_version();
    }
}
