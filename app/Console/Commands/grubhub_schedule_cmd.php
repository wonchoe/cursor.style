<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Grubhub;


class grubhub_schedule_cmd extends Command
{
    
    protected $signature = 'grubhub:schedule';
    protected $description = 'Schedule grabhub';
  
    public function setSchedule($str, $item){
        
        $response = Http::async()->withHeaders([
                            'accept' => '*/*',
                            'user-agent' => 'GHDelivery/4.54 (iPad; iOS 15.1; Scale/2.00)',
                            'authorization' => 'Bearer '.$str->at
                        ])
                        ->post('https://api-managed-delivery-gtm.grubhub.com/deliverymobilegateway/sws/v1/blocks/open/'.$item['id'].'/pickup?includeRemoved=true')->then(function($response){
//                            echo $response->body();
                        });
        $response->wait();
    }
    
    public function checkSchedule($str) {
        $response = Http::withHeaders([
                            'accept' => '*/*',
                            'user-agent' => 'GHDelivery/4.54 (iPad; iOS 15.1; Scale/2.00)',
                            'authorization' => 'Bearer '.$str->at
                        ])
                        ->get('https://api-managed-delivery-gtm.grubhub.com/deliverymobilegateway/sws/v1/blocks/current?includeRemoved=false')->json();        
        foreach($response as $item) {
            if (($item['type'] == 'OPEN') && (intval($item['couriers_needed']) > 0)){  
                $datetime = intval(date('G', strtotime($item["start"])));
                if (($datetime>=11) && ($datetime<=19)){
                    $this->setSchedule($str, $item);
                }
            }
        }
	return $response;
    }

public function checkDate($response){
if (!empty($response)) {
    usort($response, function ($a, $b) {
        return strtotime($b['updated_date']) <=> strtotime($a['updated_date']);
    });

    $lastUpdatedDate = $response[0]['updated_date'] ?? null;

    return $lastUpdatedDate ? $lastUpdatedDate : "error";
} else {
    return "error";
}
}
    
    public function handle()
    {
        date_default_timezone_set("America/New_York");
        $str = Grubhub::first();  
               
        if (date("w") == '6'){
                // $response = $this->checkSchedule($str);
        }
	$response = $this->checkDate($response);

	$result = [
	    "current_datetime" => date("Y-m-d\TH:i:s\Z"), // Поточна дата і час у форматі ISO 8601
	    "last_updated_date" => $response
	];
	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return Command::SUCCESS;
    }
}
