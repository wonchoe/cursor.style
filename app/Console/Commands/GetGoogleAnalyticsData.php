<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use App\Models\reports;
use Carbon\Carbon;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;

class GetGoogleAnalyticsData extends Command
{
    protected $signature = 'GetGoogleAnalyticsData';
    protected $description = 'Get GA4 installs and uninstalls';

    public function handle()
    {
        $ga4PropertyId = '368458115'; 
        $credentialsPath = storage_path('../google.json');

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google_Service_AnalyticsData($client);

        // Отримуємо install + uninstall
        $request = new Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [['startDate' => 'today', 'endDate' => 'today']],
            'dimensions' => [['name' => 'eventName']],
            'metrics' => [['name' => 'activeUsers']],
            'dimensionFilter' => [
                'orGroup' => [
                    'expressions' => [
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'install']]],
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'uninstall']]],
                    ],
                ],
            ]            
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4PropertyId, $request);

        $installs = 0;
        $uninstalls = 0;

        foreach ($response->getRows() ?? [] as $row) {
            $eventName = $row->getDimensionValues()[0]->getValue();
            $count = (int) $row->getMetricValues()[0]->getValue();
            if ($eventName === 'install') {
                $installs = $count;
            }
            if ($eventName === 'uninstall') {
                $uninstalls = $count;
            }
        }

        $today = date('Y-m-d');
        $reports = reports::firstOrNew(['date' => $today, 'project' => 'youtube_skins_com']);
        $reports->installs = $installs;
        $reports->uninstalls = $uninstalls;
        $reports->save();


        $logFile = storage_path('logs/command_output.log');
        File::append($logFile, "[" . now() . "] installs=$installs, uninstalls=$uninstalls\n");

        $this->info("GA4: installs=$installs, uninstalls=$uninstalls");
        return Command::SUCCESS;
    }
}
