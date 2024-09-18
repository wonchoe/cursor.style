<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Analytic as Analytic;
use App\uninstalled as Uninstall;
use App\Http\Controllers\Controller;
use DB;

class DashboardController extends Controller {

    public function show() { 
                return view('admin.index');
    }

    public function showUninstalled() {
        $dates = [];
        $responses = [];
        $r = Uninstall::orderBy('date', 'desc')->paginate(30);
        foreach ($r as $item) {
            $dates[] = $item->date;
            $responses[] = $item->count;
        }
        if (!empty($dates)) {
            $dates = implode(',', $dates);
            $responses = implode(',', $responses);
        }
        return view('admin.uninstalled', ['analyticDate' => $r, 'dates' => $dates, 'responses' => $responses]);
    }

    //
}
