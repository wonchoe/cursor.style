<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use Google_Service_Webmasters;

class SitemapSubmit extends Command
{
    protected $signature = 'custom:sitemap {domain?}';
    protected $description = 'Submit one or all sitemaps to Google Search Console via API';

    // Мовні коди
    protected $localeCodes = [
        'am', 'ar', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fil', 'fr', 'gu', 'he',
        'hi', 'hr', 'hu', 'id', 'it', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'ms', 'nl', 'no', 'pl', 'pt', 'ro', 'ru',
        'sk', 'sl', 'sr', 'sv', 'sw', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh'
    ];

    public function handle()
    {
        $jsonKeyFilePath = storage_path('../google.json'); // Зміни шлях, якщо треба!
        if (!file_exists($jsonKeyFilePath)) {
            $this->error("Service Account JSON file not found: $jsonKeyFilePath");
            return 1;
        }

        $client = new Google_Client();
        $client->setAuthConfig($jsonKeyFilePath);
        $client->setScopes(['https://www.googleapis.com/auth/webmasters']);
        $service = new Google_Service_Webmasters($client);

        // Визначаємо, що сабмітити
        $sites = [];

        $paramDomain = $this->argument('domain');

        if ($paramDomain) {
            // Якщо передано конкретний домен (напр. am.cursor.style, cursor.style)
            $domain = $paramDomain;
            $site = "sc-domain:$domain";
            $sitemap = "https://$domain/sitemap.xml";
            $sites[] = [
                'site' => $site,
                'sitemap' => $sitemap
            ];
            $this->info("Submit ONLY for: $domain");
        } else {
            // Якщо параметр не заданий — сабмітимо всі
            $sites[] = [
                'site' => 'sc-domain:cursor.style',
                'sitemap' => 'https://cursor.style/sitemap.xml'
            ];
            foreach ($this->localeCodes as $lang) {
                $sites[] = [
                    'site' => "sc-domain:{$lang}.cursor.style",
                    'sitemap' => "https://{$lang}.cursor.style/sitemap.xml"
                ];
            }
            $this->info("Submit for ALL domains (default + all languages)");
        }

        $success = 0;
        $failed = 0;
        $this->line("=== Submit Sitemaps ===");

        foreach ($sites as $item) {
            $siteUrl = $item['site'];
            $sitemapUrl = $item['sitemap'];

            $this->line("Submitting [$siteUrl] => $sitemapUrl ...");

            try {
                $service->sitemaps->submit($siteUrl, $sitemapUrl);
                $this->info("SUCCESS: $sitemapUrl for $siteUrl");
                $success++;
            } catch (\Exception $e) {
                $this->error("FAILED: $sitemapUrl for $siteUrl");
                $this->error("  > Google API error: ".$e->getMessage());
                $failed++;
            }
        }

        $this->info("=== Done! Success: $success, Failed: $failed ===");
        return 0;
    }
}
