<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reports;
use Carbon\Carbon;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;

class GetGoogleAnalyticsExtension extends Command
{
    protected $signature = 'custom:GetGoogleAnalyticsExtension';
    protected $description = 'Get Cursor Style Extension GA4 installs and uninstalls';

    /**
     * Отримати статистику для певної події за дату
     */
    public function fetchEventCount($analytics, $propertyId, $date, $eventName)
    {
        $request = new \Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [['startDate' => $date, 'endDate' => $date]],
            'dimensions' => [
                ['name' => 'eventName'],
            ],
            'metrics' => [['name' => 'totalUsers']],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'eventName',
                    'stringFilter' => ['value' => $eventName],
                ]
            ]
        ]);

        $response = $analytics->properties->runReport('properties/' . $propertyId, $request);
        $rows = $response->getRows() ?? [];
        if (count($rows) > 0) {
            return (int) $rows[0]->getMetricValues()[0]->getValue();
        }
        return 0;
    }

    public function handle()
    {
        $ga4PropertyId = '368458115';
        $credentialsPath = storage_path('../google.json');

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');
        $analytics = new Google_Service_AnalyticsData($client);

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $eventNames = [
            'extension_install',
            'extension_active',
            'extension_update',
        ];

        // По кожній даті збираємо події
        foreach ([$today, $yesterday] as $date) {
            $stats = [];
            foreach ($eventNames as $event) {
                $stats[$event] = $this->fetchEventCount($analytics, $ga4PropertyId, $date, $event);
            }

            // Зберігаємо в базу, наприклад так (якщо потрібні інші поля - додай)
            Reports::updateOrCreate(
                ['date' => $date, 'project' => 'cursor_style'],
                [
                    'extension_install' => $stats['extension_install'] ?? 0,
                    'extension_active' => $stats['extension_active'] ?? 0,
                    'extension_update' => $stats['extension_update'] ?? 0,
                ]
            );

            $this->info("GA4 $date: installs={$stats['extension_install']}, active={$stats['extension_active']}, updates={$stats['extension_update']}");
        }

        return Command::SUCCESS;
    }
}
