<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $host = request()->getHost(); // напр. ar.cursor.style
        $prefix = explode('.', $host)[0]; // "ar", "fr", "cursor"

        // Якщо основний сайт — віддаємо як є
        if ($host === 'cursor.style') {
            return response()->file(public_path('sitemaps/sitemap.xml'), [
                'Content-Type' => 'application/xml'
            ]);
        }

        // Завантажуємо основний sitemap
        $sitemap = file_get_contents(public_path('sitemaps/sitemap.xml'));

        // Замінюємо всі посилання https://cursor.style/ → https://ar.cursor.style/
        $sitemap = str_replace('https://cursor.style', "https://{$host}", $sitemap);

        return response($sitemap, 200)->header('Content-Type', 'application/xml');
    }
}
