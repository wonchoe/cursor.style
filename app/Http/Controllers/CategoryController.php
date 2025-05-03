<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categories;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;

class CategoryController extends Controller
{
    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'base_name' => 'required|string|max:255',
            'base_name_en' => 'required|string|max:256',
            'base_name_es' => 'required|string|max:256',
            'alt_name' => 'required|string|max:255|unique:categories',
            'priority' => 'nullable|integer',
            'installed' => 'nullable|integer',
            'description' => 'required|string',
            'short_descr' => 'required|string',
            'img' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        $path = $request->file('img')->store('categories', 'public');

        categories::create([
            'base_name' => $validated['base_name'],
            'base_name_en' => $validated['base_name_en'],
            'base_name_es' => $validated['base_name_es'],
            'alt_name' => $validated['alt_name'],
            'priority' => $validated['priority'] ?? 0,
            'installed' => $validated['installed'] ?? 0,
            'description' => $validated['description'],
            'short_descr' => $validated['short_descr'],
            'img' => $path,
        ]);

        // Update translations file dynamically
        $translationsFile = resource_path('lang/en/collections.php');

        if (file_exists($translationsFile)) {
            $translations = include $translationsFile;

            $translations[$validated['alt_name']] = $validated['base_name_en'];
            $translations[$validated['alt_name'] . '_short_descr'] = $validated['short_descr'];
            $translations[$validated['alt_name'] . '_descr'] = $validated['description'];

            $export = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            file_put_contents($translationsFile, $export);
        }

        return redirect()->route('categories.create')->with('success', 'Категорію додано!');
    }
}
