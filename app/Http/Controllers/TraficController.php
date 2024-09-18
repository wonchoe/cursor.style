<?php

namespace App\Http\Controllers;

use App\models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraficController extends Controller {

    public function pixel($web) {
//        DB::table('trafic')->updateOrInsert(
//                ['site' => $request->web, 'count' => '1'], 
//                ['count' => DB::raw('count + 1')]
//        )->dump();;
        DB::statement('INSERT INTO traf(website, count) VALUE("' . $web . '",1) ON DUPLICATE KEY UPDATE count=count+1');
    }

    public function index(Request $request) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'];
            $res = models\Trafic::firstOrNew(['ip' => $ip]);
//            $res->ip = $request->ip;
//            $res->site_id = htmlspecialchars($request->site_id);
//            $res->installed = $request->installed;
            $res->ip = $ip;
            $res->site_id = 0;
            $res->installed = 1;
            $res->save();
            //$result = ['result' => true, 'installed' => (($request->installed <> 0) ? true : false), 'site' => $res->site_id];
            $result = ['result' => true, 'installed' => true, 'site' => 0];
        } catch (\Illuminate\Database\QueryException $e) {
            abort(404);
            return;
        }

        return $result;
    }

    public function show() {
        $res = DB::select('SELECT site_id, date(created_at) as cdate , sum(installed) as inst, count(*) as cnt FROM trafics WHERE archived=0 GROUP BY cdate,site_id ORDER BY cdate DESC');
        return view('admin.install', ['res' => $res]);
    }

    public function showArchived() {
        $res = DB::select('SELECT site_id, date(created_at) as cdate , sum(installed) as inst, count(*) as cnt FROM trafics WHERE archived=1 GROUP BY cdate,site_id ORDER BY cdate DESC');
        return view('admin.installArchived', ['res' => $res]);
    }

    public function archive() {
        $res = DB::update('UPDATE trafics SET archived=1');
        return ['result' => true];
    }

}
