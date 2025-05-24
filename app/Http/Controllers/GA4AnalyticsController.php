<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;

class GA4AnalyticsController extends Controller
{
    public function getInstallCount()
    {
	config(['app.debug' => true]);
        $propertyId = '368458115';
        $credentialsPath = storage_path('../google.json');

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google_Service_AnalyticsData($client);

        $request = new Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [
                ['startDate' => 'today', 'endDate' => 'today']
            ],
            'dimensions' => [
                ['name' => 'eventName']
            ],
            'metrics' => [
                ['name' => 'eventCount']
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'eventName',
                    'stringFilter' => ['value' => 'install']
                ]
            ]
        ]);

        $response = $analytics->properties->runReport('properties/' . $propertyId, $request);

        $rows = $response->getRows();
        $count = $rows[0]->getMetricValues()[0]->getValue() ?? 0;

        return response()->json([
            'install_count_today' => (int) $count
        ]);
    }
}
