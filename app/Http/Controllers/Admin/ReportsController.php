<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Reports;
use App\Models\AdsenseReport;
use Carbon\Carbon;
use App\Models\Grubhub;
use App\Http\Controllers\Controller;

class ReportsController extends Controller {


    public function index() {

        // Get the last 5 days
        $lastFiveDays = Carbon::now()->subDays(7);

        // Fetch data grouped by project
        $projects = reports::where('date', '>=', $lastFiveDays)
                ->select('*')
                ->orderBy('date', 'desc') // Сортуємо за датою в порядку спадання
                ->orderBy('project')
                ->get()
                ->groupBy('project');

        $projectNameMapping = [
            'ad_skipper' => 'Ad Skipper',
            'cursor_land_com' => 'Cursor Land',
            'cursor_style' => 'Cursor Style',
            'fb_zone' => 'Facebook themes',
            'youtube_skins_com' => 'Youtube skins',
        ];

        
        foreach ($projects as $project => $data) {
            // Прорахунок процентів анулювань для всіх даних
            foreach ($data as $report) {
                if ($report->installs > 0 && $report->uninstalls >= 0) {
                    $report->uninstall_rate = round(($report->uninstalls / $report->installs) * 100, 2) . '%';
                } else {
                    $report->uninstall_rate = '0%';
                }
            }

            // Додаємо порівняння для кожного дня
            $previousData = null;

            // Сортуємо дані в реверсному порядку для порівняння
            foreach ($data->reverse() as $report) {
                if ($previousData) {
                    // Порівняння з попереднім днем
                    if ($report->feedbacks_total !== 0) {
                        if ($report->feedbacks_total > $previousData->feedbacks_total) {
                            $report->feedbacks_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $report->feedbacks_total - $previousData->feedbacks_total;
                            $report->feedbacks_total_comparison = ' (+' . $difference . ')'; // No "up"
                        } elseif ($report->feedbacks_total < $previousData->feedbacks_total) {
                            $report->feedbacks_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $previousData->feedbacks_total - $report->feedbacks_total;
                            $report->feedbacks_total_comparison = ' (' . '-' . $difference . ')'; // No "down"
                        }
                    }

                    // Compare overal_rank
                    if ($report->overal_rank !== 0) {
                        if ($report->overal_rank > $previousData->overal_rank) {
                            $report->overal_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $report->overal_rank - $previousData->overal_rank;
                            $report->overal_rank_comparison = ' (+' . $difference . ')'; // No "up"
                        } elseif ($report->overal_rank < $previousData->overal_rank) {
                            $report->overal_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $previousData->overal_rank - $report->overal_rank;
                            $report->overal_rank_comparison = ' (' . '-' . $difference . ')'; // No "down"
                        }
                    }

                    // Compare cat_rank
                    if ($report->cat_rank !== 0) {
                        if ($report->cat_rank > $previousData->cat_rank) {
                            $report->cat_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $report->cat_rank - $previousData->cat_rank;
                            $report->cat_rank_comparison = ' (+' . $difference . ')'; // No "up"
                        } elseif ($report->cat_rank < $previousData->cat_rank) {
                            $report->cat_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $previousData->cat_rank - $report->cat_rank;
                            $report->cat_rank_comparison = ' (' . '-' . $difference . ')'; // No "down"
                        }
                    }
                }

                // Запам'ятовуємо поточні дані як попередні для наступної ітерації
                $previousData = $report;

                // Додати ім'я проекту
                $report->project_name = $projectNameMapping[$project] ?? $project; // Use original if not found
            }
        }

        
    //     $grub_hub = Grubhub::select('updated')->get()->filter(function ($item) {
    //         $twoHoursAgo = Carbon::now()->subHours(2);
    //         return Carbon::parse($item->updated)->gt($twoHoursAgo);
    //     });

    //     if ($grub_hub->isEmpty()) {
    //         $grub_hub = false;
    //     } else {
    //         $grub_hub = true;
    //     }

	// $grubhub_schedule_response = json_decode(@file_get_contents(base_path('grubhub.txt')), true);               
	// if (empty($grubhub_schedule_response) || json_last_error() !== JSON_ERROR_NONE) {
	//     $grubhub_schedule_response = [
	//         'current_datetime' => "1973-01-01T00:00:00Z",
	//         'last_updated_date' => '1973-01-01T00:00:00Z'
	//     ];
	// }

        $reports = AdsenseReport::orderBy('date', 'desc')->take(2)->get();

        $todayReport = $reports->get(0) ?? null;
        $yesterdayReport = $reports->get(1) ?? null;

        return view('reports.index', [
        'projects' => $projects,
        'adsenseToday' => $todayReport,
        'adsenseYesterday' => $yesterdayReport,
        ]);
    }
}
