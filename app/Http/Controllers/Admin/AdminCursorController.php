<?php
namespace App\Http\Controllers\Admin;

use Google\Service\PubsubLite\CommitCursorResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Cursors;

class AdminCursorController extends Controller
{
    public function index(Request $request)
    {
        // collections для селектора
        $collections = Collection::orderBy('id','desc')->get();

        // Вибрана колекція
        $collectionId = $request->get('collection', $collections->first()?->id);

        // Курсори цієї колекції (або всі, якщо не вибрано)
        $cursors = Cursors::when($collectionId, fn($q) => $q->where('cat', $collectionId))
            ->orderByDesc('id')
            ->paginate(50);

        return view('reports.cursors', compact('collections', 'collectionId', 'cursors'));
    }

    public function create(Request $request)
    {
        // Селектор колекцій (передаємо selected)
        $collections = Collection::orderBy('id', 'desc')->get();
        $collectionId = $request->get('collection');

        return view('reports.cursors-create', compact('collections', 'collectionId'));
    }

    public function destroy($id)
    {
        $cursor = Cursors::findOrFail($id);

        // Видаляємо SVG файли
        if ($cursor->c_file) {
            Storage::disk('public')->delete($cursor->c_file);
        }
        if ($cursor->p_file) {
            Storage::disk('public')->delete($cursor->p_file);
        }

        // Видалення з мовного файлу
        $langPath = resource_path('lang/en/cursors.php');
        if (File::exists($langPath)) {
            $translations = include $langPath;
            unset($translations['c_' . $cursor->id]);
            File::put($langPath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
        }

        $cursor->delete();

        return redirect()->back()->with('success', 'Cursors deleted successfully.');
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|array',
        'name.*' => 'required|string|max:255',

        'cat_id' => 'required|array',
        'cat_id.*' => 'required|exists:categories,id',

        'c_file' => 'required|array',
        'c_file.*' => 'required|file|mimes:png,jpg,jpeg,svg',

        'p_file' => 'required|array',
        'p_file.*' => 'required|file|mimes:png,jpg,jpeg,svg',

        'offsetX' => 'required|array',
        'offsetX.*' => 'required|integer|min:0',

        'offsetY' => 'required|array',
        'offsetY.*' => 'required|integer|min:0',

        'offsetX_p' => 'required|array',
        'offsetX_p.*' => 'required|integer|min:0',

        'offsetY_p' => 'required|array',
        'offsetY_p.*' => 'required|integer|min:0',

        'schedule' => 'required|date',
    ]);

    $count = count($request->input('name'));

    for ($i = 0; $i < $count; $i++) {
        try {
            $cursorFile = $request->file('c_file')[$i];
            $pointerFile = $request->file('p_file')[$i];

            $category = Collection::findOrFail($request->input('cat_id')[$i]);
            $slugName = Str::slug($request->input('name')[$i]);
            $collectionDir = $category->id . '-' . $category->alt_name;

            // 1. Create cursor (without files yet)
            $cursor = new Cursors();
            $cursor->name = $request->input('name')[$i];
            $cursor->name_en = $cursor->name;
            $cursor->name_es = $cursor->name;
            $cursor->cat = $category->id;
            $cursor->offsetX = $request->input('offsetX')[$i];
            $cursor->offsetY = $request->input('offsetY')[$i];
            $cursor->offsetX_p = $request->input('offsetX_p')[$i];
            $cursor->offsetY_p = $request->input('offsetY_p')[$i];
            $cursor->schedule = $request->input('schedule');
            $cursor->c_file = $cursor->name;
            $cursor->p_file = $cursor->name;
            $cursor->save();

            // 2. Now we can generate filenames using the ID
            $baseFileName = "{$cursor->id}-{$slugName}";
            $cursorStoredName = "{$baseFileName}-cursor.svg";
            $pointerStoredName = "{$baseFileName}-pointer.svg";

            // 3. Save files to public storage
            Storage::disk('public')->putFileAs("collections/{$collectionDir}", $cursorFile, $cursorStoredName);
            Storage::disk('public')->putFileAs("collections/{$collectionDir}", $pointerFile, $pointerStoredName);

            // 4. Update cursor paths
            $cursor->c_file = "collections/{$collectionDir}/{$cursorStoredName}";
            $cursor->p_file = "collections/{$collectionDir}/{$pointerStoredName}";
            $cursor->save();

            // 5. Update language file (for fallback use)
            $langPath = resource_path('lang/en/cursors.php');
            $translations = File::exists($langPath) ? include($langPath) : [];
            $translations['c_' . $cursor->id] = $cursor->name;
            File::put($langPath, "<?php\n\nreturn " . var_export($translations, true) . ";\n");

        } catch (\Throwable $e) {
            report($e);
            continue;
        }
    }

    return redirect()->route('cursors.create')->with('success', 'Cursors created successfully.');
}

    public function reinitCursorLang()
    {
        $cursors = Cursors::select('id', 'name_en')->get();
        $path = resource_path('lang/en/cursors.php');
        $translations = File::exists($path) ? include $path : [];
        foreach ($cursors as $c) {
            $key = 'c_' . $c->id;
            if (!isset($translations[$key])) {
                $translations[$key] = $c->name_en;
            }
        }
        ksort($translations);
        $output = "<?php\n\nreturn [\n";
        foreach ($translations as $k => $v) {
            $escaped = addslashes($v);
            $output .= "    '$k' => '$escaped',\n";
        }
        $output .= "];\n";
        File::put($path, $output);
        return '✅ cursors.php updated successfully.';
    }

    public function reinitDb()
    {
        $collections = Collection::select('alt_name', 'base_name_en', 'description', 'short_descr')->get();
        $path = resource_path('lang/en/collections.php');
        $translations = File::exists($path) ? include $path : [];
        foreach ($collections as $col) {
            $key = $col->alt_name;
            if (!isset($translations[$key])) {
                $translations[$key] = $col->base_name_en;
            }
            if (!isset($translations["{$key}_descr"])) {
                $translations["{$key}_descr"] = $col->description;
            }
            if (!isset($translations["{$key}_short_descr"])) {
                $translations["{$key}_short_descr"] = $col->short_descr;
            }
        }
        $output = "<?php\n\nreturn [\n";
        foreach ($translations as $k => $v) {
            $escaped = addslashes($v);
            $output .= "    '$k' => '$escaped',\n";
        }
        $output .= "];\n";
        File::put($path, $output);
        $this->reinitCursorLang();
        return '✅ New keys added to collections.php (existing kept unchanged).';
    }
}
