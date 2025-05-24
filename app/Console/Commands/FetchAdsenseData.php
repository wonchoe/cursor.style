<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleAdsenseService;
use App\Models\AdsenseReport;
use Carbon\Carbon;

class FetchAdsenseData extends Command
{
    protected $signature = 'adsense:fetch';
    protected $description = 'Fetch AdSense earnings for today and yesterday and store to DB';

    public function handle(GoogleAdsenseService $adsense)
    {
        foreach (['YESTERDAY', 'TODAY'] as $range) {
            $report = $adsense->fetchEarnings($range);
            $date = now('Europe/Kyiv')->format('Y-m-d');

            if ($range === 'YESTERDAY') {
                $date = now('Europe/Kyiv')->subDay()->format('Y-m-d');
            }

            if (!$report || empty($report['rows'])) {
                AdsenseReport::updateOrCreate(
                    ['date' => $date],
                    [
                        'estimated_earnings' => 0,
                        'clicks'             => 0,
                        'impressions'        => 0,
                        'page_views'         => 0,
                        'impressions_rpm'    => 0,
                        'cost_per_click'     => 0,
                    ]
                );

                $this->warn("No data for $range — zeroed in DB.");
                continue;
            }

            foreach ($report['rows'] as $row) {
                $cells = collect($row['cells'])->pluck('value');
                $date = $cells[0] ?? null;

                if (!$date) continue;

                AdsenseReport::updateOrCreate(
                    ['date' => $date],
                    [
                        'estimated_earnings' => $cells[1] ?? 0,
                        'clicks'             => $cells[2] ?? 0,
                        'impressions'        => $cells[3] ?? 0,
                        'page_views'         => $cells[4] ?? 0,
                        'impressions_rpm'    => $cells[5] ?? 0,
                        'cost_per_click'     => $cells[6] ?? 0,
                    ]
                );
            }

            $this->info("Saved report for $range.");
        }

        return Command::SUCCESS;
    }
}
