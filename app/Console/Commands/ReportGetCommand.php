<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\reports;

class ReportGetCommand extends Command {

    protected $signature = 'yandex:report';
    protected $description = 'Get analytics report';

    public function handle() {
        date_default_timezone_set("America/New_York");
        $access_token = env('YANDEX_METRIKA_TOKEN');
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $api_endpoint = 'https://api-metrika.yandex.net/stat/v1/data/bytime?metrics=ym:s:goal276893276users,ym:s:goal276893278users,ym:s:goal277063809users,ym:s:goal277063810users,ym:s:goal279424905users,ym:s:goal279424922users,ym:s:goal277063811users,ym:s:goal277063812users,ym:s:goal317222294users,ym:s:goal317222328users&date1=' . $yesterday . '&date2=' . $today . '&group=day&ids=91861407,91843199,91861405,91861418';
        $this->info($api_endpoint);
        $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/x-yametrika+json'
                ])->accept('application/json')->get($api_endpoint)->json();

        if (isset($response['totals'][9])) {
            // CURSOR-LAND.COM
            $reports = reports::firstOrNew(['date' => $today, 'project' => 'cursor_land_com']);
            $reports->installs = $response['totals'][0][1];
            $reports->uninstalls = $response['totals'][1][1];
            $reports->save();

            $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'cursor_land_com']);
            $reports->installs = $response['totals'][0][0];
            $reports->uninstalls = $response['totals'][1][0];
            $reports->save();


            $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'cursor_style']);
            $reports->installs = $response['totals'][2][0];
            $reports->uninstalls = $response['totals'][3][0];
            $reports->save();

            // FB.ZONE
            $reports = reports::firstOrNew(['date' => $today, 'project' => 'fb_zone']);
            $reports->installs = $response['totals'][4][1];
            $reports->uninstalls = $response['totals'][5][1];
            $reports->save();

            $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'fb_zone']);
            $reports->installs = $response['totals'][4][0];
            $reports->uninstalls = $response['totals'][5][0];
            $reports->save();

            // YOUTUBE-SKINS.COM
            $reports = reports::firstOrNew(['date' => $today, 'project' => 'youtube_skins_com']);
            $reports->installs = $response['totals'][6][1];
            $reports->uninstalls = $response['totals'][7][1];
            $reports->save();

            $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'youtube_skins_com']);
            $reports->installs = $response['totals'][6][0];
            $reports->uninstalls = $response['totals'][7][0];
            $reports->save();

            // AD SKIPPER
            $reports = reports::firstOrNew(['date' => $today, 'project' => 'ad_skipper']);
            $reports->installs = $response['totals'][8][1];
            $reports->uninstalls = $response['totals'][9][1];
            $reports->save();

            $reports = reports::firstOrNew(['date' => $yesterday, 'project' => 'ad_skipper']);
            $reports->installs = $response['totals'][8][0];
            $reports->uninstalls = $response['totals'][9][0];
            $reports->save();
        } else {
            $this->info('No totals');
        }

        $this->info(json_encode($response));
        return Command::SUCCESS;
    }
}
