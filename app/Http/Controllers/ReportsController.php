<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\reports;
use Carbon\Carbon;

class ReportsController extends Controller {

    public function index() {

        // Get the last 5 days
        $lastFiveDays = Carbon::now()->subDays(5);

// Fetch data grouped by project
        $projects = Reports::where('date', '>=', $lastFiveDays)
                ->select('*')
                ->orderBy('project')
                ->get()
                ->groupBy('project');

foreach ($projects as $project => $data) {
    // Get today's and yesterday's date
    $today = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    // Fetch today's and yesterday's data
    $todayData = $data->where('date', $today)->first();
    $yesterdayData = $data->where('date', $yesterday)->first();

    $projectNameMapping = [
        'ad_skipper' => 'Ad Skipper',
        'cursor_land_com' => 'Cursor Land',
        'cursor_style' => 'Cursor Style',
        'fb_zone' => 'Facebook themes',
        'youtube_skins_com' => 'Youtube skins',
    ];
    
    // Add comparison results to today's data
    if ($todayData) {
        
        $projectDisplayName = $projectNameMapping[$project] ?? $project; // Use original if not found
        // Initialize signs
        $todayData->feedbacks_sign = '';
        $todayData->overal_rank_sign = '';
        $todayData->cat_rank_sign = '';
        
        // Initialize comparison values
        $todayData->feedbacks_total_comparison = '';
        $todayData->overal_rank_comparison = '';
        $todayData->cat_rank_comparison = '';

        // Check if yesterday's data exists
        if ($yesterdayData) {
            // Compare feedbacks_total
            if ($todayData->feedbacks_total > $yesterdayData->feedbacks_total) {
                $todayData->feedbacks_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                $difference = $todayData->feedbacks_total - $yesterdayData->feedbacks_total;
                $todayData->feedbacks_total_comparison = ' (+' . $difference . ')'; // No "up"
            } elseif ($todayData->feedbacks_total < $yesterdayData->feedbacks_total) {
                $todayData->feedbacks_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                $difference = $yesterdayData->feedbacks_total - $todayData->feedbacks_total;
                $todayData->feedbacks_total_comparison = ' (' . '-' . $difference . ')'; // No "down"
            }

            // Compare overal_rank
            if ($todayData->overal_rank > $yesterdayData->overal_rank) {
                $todayData->overal_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                $difference = $todayData->overal_rank - $yesterdayData->overal_rank;
                $todayData->overal_rank_comparison = ' (+' . $difference . ')'; // No "up"
            } elseif ($todayData->overal_rank < $yesterdayData->overal_rank) {
                $todayData->overal_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                $difference = $yesterdayData->overal_rank - $todayData->overal_rank;
                $todayData->overal_rank_comparison = ' (' . '-' . $difference . ')'; // No "down"
            }

            // Compare cat_rank
            if ($todayData->cat_rank > $yesterdayData->cat_rank) {
                $todayData->cat_rank_sign = '<i class="fas fa-arrow-up text-emerald-500"></i>';
                $difference = $todayData->cat_rank - $yesterdayData->cat_rank;
                $todayData->cat_rank_comparison = ' (+' . $difference . ')'; // No "up"
            } elseif ($todayData->cat_rank < $yesterdayData->cat_rank) {
                $todayData->cat_rank_sign = '<i class="fas fa-arrow-down text-orange-500"></i>';
                $difference = $yesterdayData->cat_rank - $todayData->cat_rank;
                $todayData->cat_rank_comparison = ' (' . '-' . $difference . ')'; // No "down"
            }
        }
        
        $todayData->project_name = $projectDisplayName;
    }
}

        return view('reports', compact('projects'));
    }
}
