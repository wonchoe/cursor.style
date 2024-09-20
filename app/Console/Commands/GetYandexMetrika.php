<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Analytic;
use Carbon\Carbon; // Correct import statement for Carbon
use Illuminate\Support\Facades\File; // Import the File facade

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

        $logFile = storage_path('logs/command_output.log');
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        File::append($logFile, "[$timestamp] " . json_encode($response) . PHP_EOL);

	if (isset($response['totals'][0]) && isset($response['totals'][1])) {
	        $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);
	        $stat->installs_ya = $response['totals'][0];
	        $stat->uninstalls_ya = $response['totals'][1];
	        $stat->save();
	}

        $this->info(json_encode($response));
        return Command::SUCCESS;
        
    }
}
