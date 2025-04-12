<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Grubhub;

class grubhub_schedule_reverse_cmd extends Command
{
    protected $signature = 'grubhub:schedule_reverse';
    protected $description = 'Schedule grabhub reverse';
   
    
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
        
        $response = array_reverse($response);

        foreach($response as $item) {
            if (($item['type'] == 'OPEN') && (intval($item['couriers_needed']) > 0)){  
                $datetime = intval(date('G', strtotime($item["start"])));
                if (($datetime>=11) && ($datetime<=19)){
                    $this->setSchedule($str, $item);
                }
            }
        }
    }
    
    public function handle()
    {
        date_default_timezone_set("America/New_York");
        $str = Grubhub::first();  
                
        if (date("w") == '6'){
             //   $this->checkSchedule($str);
        }
        return Command::SUCCESS;
    }
}
