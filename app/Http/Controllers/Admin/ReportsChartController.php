<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Reports;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ReportsChartController extends Controller
{
    public function __invoke(Request $request)
    {
        $start = Carbon::parse($request->input('start_date', now()->subDays(6)));
        $end = Carbon::parse($request->input('end_date', now()));
        $projects = $request->input('projects', []);

        $query = Reports::whereBetween('date', [$start, $end])
            ->orderBy('date', 'asc');


        if (!empty($projects)) {
            $query->whereIn('project', $projects);
        }



        $data = $query->get()->groupBy('project')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'installs' => $item->installs,
                    'uninstalls' => $item->uninstalls,
                    'feedbacks' => $item->feedbacks_total ?? 0,
                ];
            });
        });
        
        
        return response()->json($data);
    }
}
