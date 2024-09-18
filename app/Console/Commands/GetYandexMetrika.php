<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Analytic;

class GetYandexMetrika extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetYandexMetrika';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Installs and Uninstalls';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set("America/New_York");
        $response = Http::get('https://cursor-land.com/yatok');
        $response = json_decode($response->body());
        if (!$response->ya_token){
            return;
        }

        $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$response->ya_token,
                    'Content-Type' => 'application/x-yametrika+json'
                ])->accept('application/json')->get('https://api-metrika.yandex.net/stat/v1/data?dimensions=ym:s:trafficSource&metrics=ym:s:goal277063809users,ym:s:goal277063810users&id=91861405&date1='.date("Y-m-d"))->json();

        $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
        $stat->installs_ya = $response['totals'][0];
        $stat->uninstalls_ya = $response['totals'][1];
        $stat->save();
        return Command::SUCCESS;
        
    }
}
