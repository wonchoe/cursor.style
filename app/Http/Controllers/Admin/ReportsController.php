<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Reports;
use App\Models\AdsenseReport;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function index()
    {
        $lastFiveDays = Carbon::now()->subDays(8);
        $projectNameMapping = [
            'ad_skipper' => 'Ad Skipper',
            'cursor_land_com' => 'Cursor Land',
            'cursor_style' => 'Cursor Style',
            'fb_zone' => 'Facebook themes',
            'youtube_skins_com' => 'Youtube skins',
        ];

        // Fetch and group reports
        $projects = Reports::where('date', '>=', $lastFiveDays)
            ->orderBy('date', 'desc')
            ->orderBy('project')
            ->get()
            ->groupBy('project');

        foreach ($projects as $project => $data) {
            // Calculate uninstall rate and set project name
            foreach ($data as $report) {
                $report->uninstall_rate = ($report->installs > 0 && $report->uninstalls >= 0)
                    ? round(($report->uninstalls / $report->installs) * 100, 2) . '%'
                    : '0%';
                $report->project_name = $projectNameMapping[$project] ?? $project;
            }

            // Compare with previous day in reverse order
            $previousData = null;
            foreach ($data->reverse() as $report) {
                if ($previousData) {
                    foreach (['rating_value', 'feedbacks_total', 'overal_rank', 'cat_rank', 'extension_install', 'extension_active', 'extension_update'] as $metric) {
                        if (isset($previousData->$metric) && $report->$metric !== null) {
                            // Calculate difference with 3 decimal places precision
                            $difference = round((float) $report->$metric - (float) $previousData->$metric, 3);
                            $sign = $difference > 0 ? 'up' : ($difference < 0 ? 'down' : '');
                            if ($sign) {
                                $report->{$metric . '_sign'} = "<i class='fas fa-arrow-$sign text-" . ($sign === 'up' ? 'emerald' : 'orange') . "-500'></i>";
                                $report->{$metric . '_comparison'} = sprintf(' (%s%.2f)', $difference > 0 ? '+' : '-', abs($difference));
                            } else {
                                // If difference is 0, set both sign and comparison to empty
                                $report->{$metric . '_sign'} = '';
                                $report->{$metric . '_comparison'} = '';
                            }
                        }
                    }
                }
                $previousData = $report;
            }
        }

        // Fetch Adsense reports
        $reports = AdsenseReport::orderBy('date', 'desc')->take(2)->get();
        $adsenseToday = $reports->get(0) ?? null;
        $adsenseYesterday = $reports->get(1) ?? null;

        return view('reports.index', [
            'projects' => $projects,
            'adsenseToday' => $adsenseToday,
            'adsenseYesterday' => $adsenseYesterday,
        ]);
    }
}