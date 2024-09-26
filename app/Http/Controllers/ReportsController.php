<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\reports;
use Carbon\Carbon;

class ReportsController extends Controller {

    public function index() {
        // Отримуємо останні 5 днів
        $lastFiveDays = Carbon::now()->subDays(5);

        // Отримуємо дані, згруповані за проектом
        $projects = reports::where('date', '>=', $lastFiveDays)
                ->select('*')
                ->orderBy('date', 'desc')
                ->orderBy('project')
                ->get()
                ->groupBy('project');

        foreach ($projects as $project => $data) {
            // Отримуємо сьогоднішню та вчорашню дати
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            // Отримуємо дані за сьогодні та вчора
            $todayData = $data->where('date', $today)->first();
            $yesterdayData = $data->where('date', $yesterday)->first();

            $projectNameMapping = [
                'ad_skipper' => 'Ad Skipper',
                'cursor_land_com' => 'Cursor Land',
                'cursor_style' => 'Cursor Style',
                'fb_zone' => 'Facebook themes',
                'youtube_skins_com' => 'Youtube skins',
            ];
            
            // Додаємо результати порівняння до сьогоднішніх даних
            if ($todayData) {
                $projectDisplayName = $projectNameMapping[$project] ?? $project; // Використовуємо оригінал, якщо не знайдено
                // Ініціалізуємо знаки
                $todayData->feedbacks_sign = '';
                $todayData->overal_rank_sign = '';
                $todayData->cat_rank_sign = '';
                
                // Ініціалізуємо значення порівняння
                $todayData->feedbacks_total_comparison = '';
                $todayData->overal_rank_comparison = '';
                $todayData->cat_rank_comparison = '';

                // Перевіряємо, чи існують дані за вчора
                if ($yesterdayData) {
                    // Порівняння для feedbacks_total
                    if ($yesterdayData->feedbacks_total !== null && $yesterdayData->feedbacks_total !== 0) {
                        if ($todayData->feedbacks_total > $yesterdayData->feedbacks_total) {
                            $todayData->feedbacks_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $todayData->feedbacks_total - $yesterdayData->feedbacks_total;
                            $todayData->feedbacks_total_comparison = ' (+' . $difference . ')';
                        } elseif ($todayData->feedbacks_total < $yesterdayData->feedbacks_total) {
                            $todayData->feedbacks_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $yesterdayData->feedbacks_total - $todayData->feedbacks_total;
                            $todayData->feedbacks_total_comparison = ' (' . '-' . $difference . ')';
                        }
                    }

                    // Порівняння для overal_rank
                    if ($yesterdayData->overal_rank !== null && $yesterdayData->overal_rank !== 0) {
                        if ($todayData->overal_rank > $yesterdayData->overal_rank) {
                            $todayData->overal_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $todayData->overal_rank - $yesterdayData->overal_rank;
                            $todayData->overal_rank_comparison = ' (+' . $difference . ')';
                        } elseif ($todayData->overal_rank < $yesterdayData->overal_rank) {
                            $todayData->overal_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $yesterdayData->overal_rank - $todayData->overal_rank;
                            $todayData->overal_rank_comparison = ' (' . '-' . $difference . ')';
                        }
                    }

                    // Порівняння для cat_rank
                    if ($yesterdayData->cat_rank !== null && $yesterdayData->cat_rank !== 0) {
                        if ($todayData->cat_rank > $yesterdayData->cat_rank) {
                            $todayData->cat_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                            $difference = $todayData->cat_rank - $yesterdayData->cat_rank;
                            $todayData->cat_rank_comparison = ' (+' . $difference . ')';
                        } elseif ($todayData->cat_rank < $yesterdayData->cat_rank) {
                            $todayData->cat_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                            $difference = $yesterdayData->cat_rank - $todayData->cat_rank;
                            $todayData->cat_rank_comparison = ' (' . '-' . $difference . ')';
                        }
                    }
                }
                
                $todayData->project_name = $projectDisplayName;
            }
        }

        return view('reports', compact('projects'));
    }
}
