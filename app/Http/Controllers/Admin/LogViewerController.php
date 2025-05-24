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
        // Список файлів логів
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

    // --- Додаємо фільтрацію по рівню, якщо потрібно ---
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
        $logs = array_values($logs); // Reindex після фільтра
    }

    $total = count($logs);

    // --- Робимо reverse, щоб найновіші були першими ---
    $logs = array_reverse($logs);

    // --- Slice без додаткового reverse! ---
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

    // --- Групування логів (один лог = один блок навіть якщо багаторядковий) ---
    protected function groupLaravelLogs($file)
    {
        $lines = explode("\n", File::get($file));
        $logs = [];
        $current = '';

        foreach ($lines as $line) {
            // Лог починається з [дата]
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
