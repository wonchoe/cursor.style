<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Grubhub;

class grubhub_cmd extends Command
{

    protected $signature = 'grubhub';

    protected $description = 'Command description';

    public function update_rt($str) {
        $data = json_decode('{"refresh_token": "' . $str->rt . '","client_id": "aaaa65f645af427bcd15d9fb3158779dd8626ff16673b4c2","exclusive_session": false}');
        $response = Http::withBody(json_encode($data), 'application/json')
                        ->withHeaders([
                            'accept' => '*/*',
                            'user-agent' => 'GHDelivery/4.54 (iPad; iOS 15.1; Scale/2.00)'
                        ])
                        ->post('https://api-managed-delivery-gtm.grubhub.com/auth/refresh')->json();

        $str->at = $response['session_handle']['access_token'];
        $str->rt = $response['session_handle']['refresh_token'];
        $str->updated = date("Y-m-d H:i:s");
        $str->save();
    }
    

    public function handle() {
        date_default_timezone_set("America/New_York");
        $str = Grubhub::first();
        
        $time_difference = (strtotime(date("Y-m-d H:i:s")) - strtotime($str->updated)) / 60;
       // dd($time_difference);
        if ($time_difference >= 55) {
            $this->update_rt($str);
        }
        
        return Command::SUCCESS;
    }
}
