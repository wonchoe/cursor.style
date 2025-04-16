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

        $request = new \Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [
                ['startDate' => 'today', 'endDate' => 'today'],
                ['startDate' => 'yesterday', 'endDate' => 'yesterday'],
            ],
            'dimensions' => [
                ['name' => 'eventName']
            ],
            'metrics' => [
                ['name' => 'activeUsers']
            ],
            'dimensionFilter' => [
                'orGroup' => [
                    'expressions' => [
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'install']]],  
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'uninstall']]],
                    ]
                ]
            ]
        ]);
        
        
        $response = $analytics->properties->runReport('properties/' . $ga4PropertyId, $request);

        $results = [
            'today' => ['install' => 0, 'uninstall' => 0],
            'yesterday' => ['install' => 0, 'uninstall' => 0]
        ];
        
        $rows = $response->getRows();
        $currentRangeIndex = 0;
        $lastEventName = null;
        
        foreach ($rows as $row) {
            $event = $row->getDimensionValues()[0]->getValue();
            $count = (int) $row->getMetricValues()[0]->getValue();
        
            // Розрахунок: кожні 2 рядки — новий dateRange (бо 2 події: install, uninstall)
            $rangeKey = $currentRangeIndex === 0 ? 'today' : 'yesterday';
        
            $results[$rangeKey][$event] = $count;
        
            if ($event === 'uninstall') {
                $currentRangeIndex++;
            }
        }

        $today = date('Y-m-d');
        $reports = reports::firstOrNew(['date' => $today, 'project' => 'cursor_style']);
        $reports->installs = $results['today']['install'];
        $reports->uninstalls = $results['today']['uninstall'];
        $reports->save();

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'cursor_style']);
        $reports->installs = $results['yesterday']['install'];
        $reports->uninstalls = $results['yesterday']['uninstall'];
        $reports->save();        

        $installs = $results['today']['install'];
        $uninstalls = $results['today']['uninstall'];
        $this->info("GA4: installs=$installs, uninstalls=$uninstalls");
        return Command::SUCCESS;
    }
}
