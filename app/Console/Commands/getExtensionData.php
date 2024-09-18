<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Analytic;

class getExtensionData extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getExtensionData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from Chrome stat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        date_default_timezone_set("America/Los_Angeles");
        
        $url = 'https://chrome-stats.com/api/detail?id=bmjmipppabdlpjccanalncobmbacckjn&date=' . date("Y-m-d");
        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0');
        curl_setopt($ch, CURLOPT_REFERER, 'https://chrome-stats.com/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:*/*'));
        $result=curl_exec($ch);
        curl_close($ch);
        $homepage = json_decode($result);

        $stat = Analytic::firstOrNew(['date' => date_format(\Carbon\Carbon::now(), 'Y-m-d')]);

        if (isset($homepage->userCount)) {
            $stat->userCount = $homepage->userCount;
        }
        if (isset($homepage->ratingValue)) {
            $stat->ratingValue = $homepage->ratingValue;
        }
        if (isset($homepage->ratingCount)) {
            $stat->ratingCount = $homepage->ratingCount;
        }
        if (isset($homepage->allRanks[0]->value)) {
            $stat->overallRank = $homepage->allRanks[0]->value;
        }
        if (isset($homepage->allRanks[1]->value)) {
            $stat->catRank = $homepage->allRanks[1]->value;
        }    
        $stat->save();  
             
        return 0;
    }

}
