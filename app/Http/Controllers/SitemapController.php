<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $host = request()->getHost();
        $prefix = explode('.', $host)[0];
        
        if ($host === 'cursor.style') {
            return response()->file(public_path('sitemaps/sitemap.xml'), [
                'Content-Type' => 'application/xml'
            ]);
        }
        
        $sitemap = file_get_contents(public_path('sitemaps/sitemap.xml'));
        $sitemap = str_replace('https://cursor.style', "https://{$host}", $sitemap);
        
        $localeCodes = [
            'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fr',
            'gu', 'he', 'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl',
            'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
        ];
        
        $sitemap = preg_replace_callback(
            '#<loc>(.*?)</loc>#',
            function ($matches) use ($localeCodes, $host) {
                $originalUrl = $matches[1];
        
                $hreflangs = '';
                foreach ($localeCodes as $code) {
                    $localizedUrl = preg_replace('#https?://[^/]+#', "https://{$code}.cursor.style", $originalUrl);
                    $hreflangs .= "\n    <xhtml:link rel=\"alternate\" hreflang=\"{$code}\" href=\"{$localizedUrl}\"/>";
                }
                $hreflangs .= "\n    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$originalUrl}\"/>";
        
                return "<loc>{$originalUrl}</loc>{$hreflangs}";
            },
            $sitemap
        );
        
        return response($sitemap, 200)
        ->header('Content-Type', 'application/xml')
        ->header('X-Robots-Tag', 'index, follow');
        
    }
}
