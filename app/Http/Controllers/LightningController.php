<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\lightning;

class LightningController extends Controller
{
    //
    public function save(Request $r){
        $stat = new lightning;
        $stat->dasher = $r->dasher;
        $stat->logs = $r->logs;
        $stat->save();        
    }
    
    public function get(Request $r){
        $stat = lightning::where('dasher', $r->dasher)->firstOrFail();
        echo $stat->logs;       
    }    
    
    public function set401(){
        return response()->json(['error' => 'uanthorized.'], 401);
    }
}
