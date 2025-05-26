<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use App\Models\Reports;
use Carbon\Carbon;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;

class GetGoogleAnalyticsData extends Command
{
    protected $signature = 'GetGoogleAnalyticsData';
    protected $description = 'Get GA4 installs and uninstalls';

public function fetchStats($analytics, $propertyId, $date, $eventLabel)
{
    $request = new \Google_Service_AnalyticsData_RunReportRequest([
        'dateRanges' => [['startDate' => $date, 'endDate' => $date]],
        'dimensions' => [
            ['name' => 'eventName'],
            ['name' => 'customEvent:event_label'],
        ],
        'metrics' => [['name' => 'totalUsers']],
        'dimensionFilter' => [
            'andGroup' => [
                'expressions' => [
                    [
                        'orGroup' => [
                            'expressions' => [
                                ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'install']]],
                                ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'uninstall']]],
                            ]
                        ]
                    ],
                    [
                        'filter' => [
                            'fieldName' => 'customEvent:event_label',
                            'stringFilter' => ['value' => $eventLabel]
                        ]
                    ]
                ]
            ]
        ]
    ]);

    $response = $analytics->properties->runReport('properties/' . $propertyId, $request);

    $stats = ['install' => 0, 'uninstall' => 0];
    foreach ($response->getRows() ?? [] as $row) {
        $event = $row->getDimensionValues()[0]->getValue(); // eventName
        $count = (int) $row->getMetricValues()[0]->getValue();
        $stats[$event] = $count;
    }
    return $stats;
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





$labels = [
    'cursor_style',
    'fb_zone',
    'ad_skipper',
    'youtube_skins_com',
    'cursor_land_com',
];

foreach ($labels as $label) {
    $todayStats = $this->fetchStats($analytics, $ga4PropertyId, $today, $label);
    $yesterdayStats = $this->fetchStats($analytics, $ga4PropertyId, $yesterday, $label);

    Reports::updateOrCreate(
        ['date' => $today, 'project' => $label],
        ['installs' => $todayStats['install'], 'uninstalls' => $todayStats['uninstall']]
    );
    Reports::updateOrCreate(
        ['date' => $yesterday, 'project' => $label],
        ['installs' => $yesterdayStats['install'], 'uninstalls' => $yesterdayStats['uninstall']]
    );

    $this->info("GA4 $label TODAY: installs={$todayStats['install']}, uninstalls={$todayStats['uninstall']}");
    $this->info("GA4 $label YESTERDAY: installs={$yesterdayStats['install']}, uninstalls={$yesterdayStats['uninstall']}");
}

        // $todayStats = $this->fetchStats($analytics, $ga4PropertyId, $today);
        // $yesterdayStats = $this->fetchStats($analytics, $ga4PropertyId, $yesterday);
        
        // reports::updateOrCreate(
        //     ['date' => $today, 'project' => 'cursor_style'],
        //     ['installs' => $todayStats['install'], 'uninstalls' => $todayStats['uninstall']]
        // );
        
        // reports::updateOrCreate(
        //     ['date' => $yesterday, 'project' => 'cursor_style'],
        //     ['installs' => $yesterdayStats['install'], 'uninstalls' => $yesterdayStats['uninstall']]
        // );
        
        // $this->info("GA4 TODAY: installs={$todayStats['install']}, uninstalls={$todayStats['uninstall']}");        

        // $this->info("GA4 YESTERDYA: installs={$yesterdayStats['install']}, uninstalls={$yesterdayStats['uninstall']}");        
        
        return Command::SUCCESS;
    }
}
// test