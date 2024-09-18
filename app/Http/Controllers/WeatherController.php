<?php

namespace App\Http\Controllers;

use App\models\userResponses as userReponse;
use App\models\Analytic as stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WeatherController extends Controller {

    public function getData(Request $request) {
        try {
            echo 'd';
        } catch (\Exception $e) {
            return ['result' => false,
                'error_message' => $e->getMessage()];
        }
    }

}
