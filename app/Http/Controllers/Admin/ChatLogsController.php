<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ChatLogsController extends Controller
{
    public function chat(Request $request)
    {
        $data = $request->all();

        // Визначаємо тип події для окремого лог-файлу
        if (!empty($data['error'])) {
            Log::channel('chatlogs')->error('[AI Check Error]', $data);
        } elseif (!empty($data['toxic'])) {
            Log::channel('chatlogs')->info('[AI Toxic Triggered]', $data);
        } else {
            Log::channel('chatlogs')->debug('[AI Log]', $data);
        }

        return response()->json(['status' => 'ok']);
    }
}