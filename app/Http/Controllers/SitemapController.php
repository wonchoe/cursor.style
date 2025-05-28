<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SitemapController extends Controller
{
    // Список підтримуваних мовних кодів (можна брати з команди)
    protected $localeCodes = [
        'en', 'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    public function index(Request $request)
    {
        $host = $request->getHost(); // наприклад uk.cursor.style
        $hostParts = explode('.', $host);

        // За замовчуванням використовуємо default sitemap
        $sitemapPath = public_path('sitemaps/default/sitemap.xml');

        // Якщо субдомен = мовний код і є sitemap для цієї мови
        $subdomain = $hostParts[0];
        if (
            in_array($subdomain, $this->localeCodes) &&
            file_exists(public_path("sitemaps/{$subdomain}/sitemap.xml"))
        ) {
            $sitemapPath = public_path("sitemaps/{$subdomain}/sitemap.xml");
        }

        // Для cursor.style без субдомену — також дефолт
        // Якщо файлу немає, то 404 або дефолт (але такого не буде, якщо команда згенерувала)

        return response()->file($sitemapPath, [
            'Content-Type' => 'application/xml',
            'X-Robots-Tag' => 'index, follow',
        ]);
    }
}
