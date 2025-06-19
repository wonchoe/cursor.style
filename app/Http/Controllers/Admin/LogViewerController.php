<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class LogViewerController extends Controller
{
    protected $logPath = 'storage/logs';

    public function index(Request $request)
    {
        $files = collect(File::files(base_path($this->logPath)))
            ->filter(fn($f) => in_array($f->getExtension(), ['log', 'txt']))
            ->map(fn($f) => $f->getFilename());

        $selected = $request->input('file', $files->last() ?? 'laravel.log');
        return view('reports.logs', compact('files', 'selected'));
    }

public function fetch(Request $request)
{
    $file = $request->input('file', 'laravel.log');
    $page = (int) $request->input('page', 1);
    $perPage = (int) $request->input('perPage', 100);
    $level = $request->input('level');

    $path = base_path($this->logPath . '/' . $file);
    if (!File::exists($path)) return response()->json([
        'lines' => [],
        'total' => 0
    ]);

    $logs = $this->groupLaravelLogs($path);

    if ($level && in_array($level, ['info', 'warning', 'error', 'debug', 'other'])) {
        $logs = array_filter($logs, function($log) use ($level) {
            if ($level === 'other') {
                return !(
                    str_contains($log, 'INFO') ||
                    str_contains($log, 'ERROR') ||
                    str_contains($log, 'WARNING') ||
                    str_contains($log, 'DEBUG')
                );
            }
            return str_contains($log, strtoupper($level));
        });
        $logs = array_values($logs); 
    }

    $total = count($logs);

    $logs = array_reverse($logs);

    $slice = array_slice($logs, ($page - 1) * $perPage, $perPage);

    return response()->json([
        'lines' => $slice,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage
    ]);
}


    public function clear(Request $request)
    {
        $file = $request->input('file', 'laravel.log');
        $path = base_path($this->logPath . '/' . $file);
        File::put($path, '');
        return response()->json(['success' => true]);
    }

    protected function groupLaravelLogs($file)
    {
        $lines = explode("\n", File::get($file));
        $logs = [];
        $current = '';

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                if ($current) $logs[] = $current;
                $current = $line;
            } else {
                $current .= "\n" . $line;
            }
        }
        if ($current) $logs[] = $current;
        return $logs;
    }
}
