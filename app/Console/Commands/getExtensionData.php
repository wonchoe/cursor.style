<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\reports;

class getExtensionData extends Command {

    protected $signature = 'getExtensionData';
    protected $description = 'Get data from Chrome stat';

    public function getReport($id, $project) {
        $url = 'https://chrome-stats.com/api/detail?id='.$id.'&date=' . date("Y-m-d");
        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0');
        curl_setopt($ch, CURLOPT_REFERER, 'https://chrome-stats.com/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:*/*'));
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);

        $reports = reports::firstOrNew(['date' => date('Y-m-d'), 'project' => $project]);

        if (isset($response->userCount)) {
            $reports->users_total = $response->userCount;
        }
        if (isset($response->ratingValue)) {
            $reports->rating_value = $response->ratingValue;
        }
        if (isset($response->ratingCount)) {
            $reports->feedbacks_total = $response->ratingCount;
        }
        if (isset($response->allRanks[0]->value)) {
            $reports->overal_rank = $response->allRanks[0]->value;
        }
        if (isset($response->allRanks[1]->value)) {
            $reports->cat_rank = $response->allRanks[1]->value;
        }
        $reports->save();  
        $this->info($reports);
    }
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        date_default_timezone_set("America/Los_Angeles");
        $this->getReport('imomahaddnhnhfggpmpbphdiobpmahof', 'youtube_skins_com');
        $this->getReport('gideponcmplkbifbmopkmhncghnkpjng', 'ad_skipper');
        $this->getReport('oodajhdbojacdmkhkiafdhicifcdjoig', 'fb_zone');
        $this->getReport('oinkhgpjmeccknjbbccabjfonamfmcbn', 'cursor_land_com');
        $this->getReport('bmjmipppabdlpjccanalncobmbacckjn', 'cursor_style');        
        return 0;
    }
}
