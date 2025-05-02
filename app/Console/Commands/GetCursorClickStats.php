<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;

class GetCursorClickStats extends Command
{
    protected $signature = 'GetCursorClickStats';
    protected $description = 'Fetch GA4 cursor_click stats grouped by cursor_id';

    public function handle()
    {
        $ga4PropertyId = '368458115'; // заміни на свій GA4 ID
        $credentialsPath = storage_path('../google.json'); // шлях до JSON ключа

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google_Service_AnalyticsData($client);

        $date = date('Y-m-d'); // або обчисли минулі години вручну

        $request = new \Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [[
                'startDate' => $date,
                'endDate' => $date,
            ]],
            'dimensions' => [
                ['name' => 'customEvent:cursor_id'],
            ],
            'metrics' => [
                ['name' => 'totalUsers'] // або totalUsers, якщо тобі треба унікальні юзери
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'eventName',
                    'stringFilter' => [
                        'value' => 'cursor_click',
                        'matchType' => 'EXACT',
                    ],
                ],
            ],
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4PropertyId, $request);

        foreach ($response->getRows() ?? [] as $row) {
            $cursorId = $row->getDimensionValues()[0]->getValue();
            $clickCount = $row->getMetricValues()[0]->getValue();
            $this->info("Cursor ID: $cursorId — Clicks: $clickCount");
        }

        return Command::SUCCESS;
    }
}
