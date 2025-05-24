<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Support\CollectionPresenter;

class AdminCollectionsController extends Controller
{
    // Список
    public function index()
    {
        $collections = Collection::with('currentTranslation')
            ->orderBy('id', 'desc')
            ->paginate(20);

        foreach ($collections as $collection) {
            $seo = CollectionPresenter::seo($collection);
            $collection->slug = $seo['slug'];
            $collection->url = $seo['url'];
            $collection->img = $seo['img'];
        }

        return view('reports.collections', compact('collections'));
    }

    // Форма створення
    public function create()
    {
        return view('reports.collections-create');
    }

    // Збереження нової колекції
public function store(Request $request)
{
    $validated = $request->validate([
        'base_name'    => 'required|string|max:255',
        'alt_name'     => 'required|string|max:255|unique:categories',
        'priority'     => 'nullable|integer',
        'installed'    => 'nullable|integer',
        'description'  => 'required|string',
        'short_descr'  => 'required|string',
        'img'          => 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
    ]);

    // 1. Створюємо колекцію (без картинки)
    $collection = Collection::create([
        'base_name'    => $validated['base_name'],
        'base_name_en' => $validated['base_name'],
        'base_name_es' => $validated['base_name'],
        'alt_name'     => $validated['alt_name'],
        'priority'     => $validated['priority'] ?? 0,
        'installed'    => $validated['installed'] ?? 0,
        'description'  => $validated['description'],
        'short_descr'  => $validated['short_descr'],
        'img'          => 'temp', // тимчасово
    ]);

    // 2. Готуємо правильний шлях
    $alt = preg_replace('/[^a-z0-9\-_]/i', '', str_replace(' ', '-', $collection->alt_name));
    $dir = "collections/{$collection->id}-{$alt}";
    $fileName = 'index.png';
    $storagePath = "$dir/$fileName";

    // 3. Копіюємо файл у потрібну папку
    $file = $request->file('img');
    $file->storeAs($dir, $fileName, 'public');

    // 4. Оновлюємо поле img у колекції
    $collection->img = "$dir/$fileName";
    $collection->save();

    // Оновлюємо translations (як у тебе)
    $translationsFile = resource_path('lang/en/collections.php');
    if (file_exists($translationsFile)) {
        $translations = include $translationsFile;

        $translations[$validated['alt_name']] = $validated['base_name'];
        $translations[$validated['alt_name'] . '_short_descr'] = $validated['short_descr'];
        $translations[$validated['alt_name'] . '_descr'] = $validated['description'];

        $export = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        file_put_contents($translationsFile, $export);
    }

    return redirect()->route('collections.index')->with('success', 'Колекцію додано!');
}



    // Видалення
    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);

        // Видалення папки collections/{id}-{alt_name}
        if ($collection->img) {
            // Витягуємо шлях до папки з img
            $dir = dirname($collection->img); // collections/{id}-{alt_name}

            // Шлях до папки у storage
            $storageDir = storage_path('app/public/' . $dir);
            if (File::exists($storageDir)) {
                File::deleteDirectory($storageDir);
            }

            // На всяк випадок — ще й legacy шлях у public (якщо раніше було)
            $legacyDir = public_path($dir);
            if (File::exists($legacyDir)) {
                File::deleteDirectory($legacyDir);
            }
        }

        // Оновлення translations
        $path = resource_path('lang/en/collections.php');
        if (File::exists($path)) {
            $translations = include $path;
            if ($collection->alt_name) {
                unset($translations[$collection->alt_name]);
                unset($translations[$collection->alt_name . '_short_descr']);
                unset($translations[$collection->alt_name . '_descr']);
            }
            ksort($translations);
            $output = "<?php\n\nreturn [\n";
            foreach ($translations as $k => $v) {
                $output .= "    '$k' => '" . addslashes($v) . "',\n";
            }
            $output .= "];\n";
            File::put($path, $output);
        }

        $collection->delete();

        return response()->json(['success' => true]);
    }

}
