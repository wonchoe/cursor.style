<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use Google_Service_AnalyticsData;
use Google_Service_AnalyticsData_RunReportRequest;
use Illuminate\Support\Facades\DB;

class GetCursorClickStats extends Command
{
    protected $signature = 'custom:GetCursorClickStats {--mode=today : today or yesterday}';
    protected $description = 'Fetch GA4 cursor_click stats grouped by cursor_id';

    public function handle()
    {
        $mode = $this->option('mode');
        $date = now()->toDateString();
        
        if ($mode === 'yesterday') {
            $date = now()->subDay()->toDateString();
            $this->info("🔁 Running in YESTERDAY mode for $date");
        } else {
            $this->info("📊 Running in TODAY mode for $date");
        }

        $ga4PropertyId = '368458115'; // заміни на свій GA4 ID
        $credentialsPath = storage_path('../google.json'); // шлях до JSON ключа

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google_Service_AnalyticsData($client);

        $request = new \Google_Service_AnalyticsData_RunReportRequest([
            'dateRanges' => [[
                'startDate' => $date,
                'endDate' => $date,
            ]],
            'dimensions' => [
                ['name' => 'customEvent:cursor_id'],
                ['name' => 'customEvent:cursor_category'], // <-- додаємо це
            ],
            'metrics' => [
                ['name' => 'eventCount']
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


        $clicksMap = [];

        foreach ($response->getRows() ?? [] as $row) {
            $cursorId = (int) $row->getDimensionValues()[0]->getValue();
            $clicks = (int) $row->getMetricValues()[0]->getValue();
        
            if ($cursorId) {
                $clicksMap[$cursorId] = $clicks;
            }
        }

        foreach (array_chunk($clicksMap, 100, true) as $batch) {
            DB::beginTransaction();
        
            try {
                foreach ($batch as $cursorId => $clicks) {
                    if ($mode === 'yesterday') {
                        $current = DB::table('cursors')->where('id', $cursorId)->value('totalClick');

                        if (!is_null($current)) {
                            $newTotal = $current + $clicks;

                            DB::table('cursors')->where('id', $cursorId)->update(['totalClick' => $newTotal]);
                        } else {
                            $this->warn("⚠️ cursor_id={$cursorId} not found. Skipping...");
                        }
                    } else {
                        DB::table('cursors')->where('id', $cursorId)->update(['todayClick' => $clicks]);
                    }
                }
        
                DB::commit();
                $this->info("✅ Transaction committed");
        
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("❌ Batch failed: " . $e->getMessage());
            }
        }
        
        

        return Command::SUCCESS;
    }
}
