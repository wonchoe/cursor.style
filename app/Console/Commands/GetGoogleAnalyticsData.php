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

    public function fetchStats($analytics, $propertyId, $date)
    {
        $request = new \Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [['startDate' => $date, 'endDate' => $date]],
            'dimensions' => [['name' => 'eventName']],
            'metrics' => [['name' => 'totalUsers']],
            'dimensionFilter' => [
                'orGroup' => [
                    'expressions' => [
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'install']]],
                        ['filter' => ['fieldName' => 'eventName', 'stringFilter' => ['value' => 'uninstall']]],
                    ]
                ]
            ]
        ]);
       

        
        $response = $analytics->properties->runReport('properties/' . $propertyId, $request);


        $stats = ['install' => 0, 'uninstall' => 0];
        foreach ($response->getRows() ?? [] as $row) {
            $event = $row->getDimensionValues()[0]->getValue();
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

        // $client->fetchAccessTokenWithAssertion(); // <- генерує токен
        // $token = $client->getAccessToken()['access_token'];
        
        // echo $token;        

        $analytics = new Google_Service_AnalyticsData($client);


        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $todayStats = $this->fetchStats($analytics, $ga4PropertyId, $today);
        $yesterdayStats = $this->fetchStats($analytics, $ga4PropertyId, $yesterday);
        
        reports::updateOrCreate(
            ['date' => $today, 'project' => 'cursor_style'],
            ['installs' => $todayStats['install'], 'uninstalls' => $todayStats['uninstall']]
        );
        
        reports::updateOrCreate(
            ['date' => $yesterday, 'project' => 'cursor_style'],
            ['installs' => $yesterdayStats['install'], 'uninstalls' => $yesterdayStats['uninstall']]
        );
        
        $this->info("GA4 TODAY: installs={$todayStats['install']}, uninstalls={$todayStats['uninstall']}");        

        $this->info("GA4 YESTERDYA: installs={$yesterdayStats['install']}, uninstalls={$yesterdayStats['uninstall']}");        
        
        return Command::SUCCESS;
    }
}
// test