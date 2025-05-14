<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\cursor;
use App\Models\categories;
use DB;
use Illuminate\Support\Str;

class ImageController extends Controller {
    public function serveSvg($category_slug, $cursor_slug)
    {
  dd($cursor_slug);
        // Якщо в кінці є .svg — обрізаємо
        if (Str::endsWith($cursor_slug, '.svg')) {
            $cursor_slug = Str::beforeLast($cursor_slug, '.svg');
        }

        $normalizedSlug = preg_replace('/-(cursor|pointer)$/', '', $cursor_slug);
        $url = "collections/{$category_slug}/{$normalizedSlug}";
        
                
        $parts = explode('-', $cursor_slug);
        
        if (count($parts) < 2) {
            abort(404);
        }

        $type = array_pop($parts); // cursor або pointer
        $id = $parts[0];


        $cursor = Cursor::where('id', $id)->firstOrFail();

        switch ($type) {
            case 'cursor':
                $filePath = 'public/' . $cursor->c_file;
                break;

            case 'pointer':
                $filePath = 'public/' . $cursor->p_file;
                break;

            default:
                abort(404);
        }

        $full_path = Storage::path($filePath);
        
        if ($type=='cursor') {
            $filename = $cursor->c_file;
        } else {
            $filename = $cursor->p_file;
        }
        dd($filename);

        if (!file_exists($full_path)) {
            abort(404);
        }

               
        $response = response()->file($full_path, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=604800',
        ]);
        $response->headers->set('Cache-Tag', 'svg');
        $response->headers->set('X-Cache-SVG', 'true');

        return $response;
    }


    public function createCursorsImagesFolders() {
        if (!is_dir(base_path() . '/storage/app/public/cursors')) {
            mkdir(base_path() . '/storage/app/public/cursors', 0755);
        }

        if (!is_link(public_path() . '/cursors')) {
            \symlink(base_path() . '/storage/app/public/cursors', public_path() . '/cursors');
        }
        return base_path() . '/storage/app/public/cursors';
    }

    public function createPointersImagesFolders() {
        if (!is_dir(base_path() . '/storage/app/public/pointers')) {
            mkdir(base_path() . '/storage/app/public/pointers', 0755);
        }

        if (!is_link(public_path() . '/pointers')) {
            \symlink(base_path() . '/storage/app/public/pointers', public_path() . '/pointers');
        }
        return base_path() . '/storage/app/public/pointers';
    }

    public function createCollectionsImagesFolders() {
        if (!is_dir(base_path() . '/storage/app/public/collection')) {
            mkdir(base_path() . '/storage/app/public/collection', 0755);
        }

        if (!is_link(public_path() . '/collection')) {
            \symlink(base_path() . '/storage/app/public/collection', public_path() . '/collection');
        }
        return base_path() . '/storage/app/public/collection';
    }

    public function getSvg(Request $r)
    {
        $cursor = cursor::findOrFail($r->id);
    
        if (!in_array($r->type, ['cursors', 'pointers'])) {
            abort(404);
        }
    
        $filename = $r->type === 'cursors' ? $cursor->c_file : $cursor->p_file;
    
        // 🧠 Категорія для підпапки
        $category = categories::findOrFail($cursor->cat);
        $subfolder = "{$category->id}-{$category->alt_name}";
    
        // 📂 Шляхи для пошуку
        $paths = [
            public_path("resources/{$r->type}/{$filename}"),
            storage_path("app/public/{$r->type}_new/{$filename}"),
            storage_path("app/public/{$r->type}/{$subfolder}/{$filename}"),
        ];
    
        // 📄 Шукаємо файл
        $file = null;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $file = file_get_contents($path);
                break;
            }
        }
    
        if (!$file) {
            abort(404, 'SVG file not found.');
        }
    
        // 🏷 Вставляємо <desc> у SVG (тільки якщо знайдений <path>)
        $search_from = round(strlen($file) / 2);
        $find = strpos($file, '<path', $search_from);
        if ($find !== false) {
            $part_one = substr($file, 0, $find);
            $part_two = substr($file, $find);
            $copyright = '<desc>cursor-style.com</desc>';
            $file = $part_one . $copyright . $part_two;
        }
    
        // 📥 Куди зберігати результат
        $cursors_folder = $this->createCursorsImagesFolders();
        $pointers_folder = $this->createPointersImagesFolders();
    
        $outputFolder = $r->type === 'cursors' ? $cursors_folder : $pointers_folder;
        file_put_contents("{$outputFolder}/{$r->id}-{$r->cursor}.svg", $file);
    
        return response($file, 200)->header('Content-Type', 'image/svg+xml');
    }
    
    

    public function show($id) {

        $p = explode('-', $id);
        $cursor = cursor::findOrFail($p[1]);
    
        if ($p[0] == 'c') {
            $filename = $cursor->c_file;
            $baseDir = 'cursors';
        } elseif ($p[0] == 'p') {
            $filename = $cursor->p_file;
            $baseDir = 'pointers';
        } else {
            abort(404);
        }
    
        $category = categories::findOrFail($cursor->cat);
        $subfolder = $category->id . '-' . $category->alt_name;
    
        $paths = [
            resource_path("{$baseDir}/{$filename}"),
            storage_path("app/public/{$baseDir}/{$filename}"),
            storage_path("app/public/{$baseDir}/{$subfolder}/{$filename}"),
        ];
    
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return response(file_get_contents($path), 200)
                    ->header('Content-Type', mime_content_type($path))
                    ->header('Pragma', 'public')
                    ->header('Cache-Control', 'max-age=86400, public')
                    ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            }
        }
    
        abort(404, 'File not found.');
    }
    
    public function showCollection($id, $alt = null)
    {
        // Якщо $alt закінчується на .png — обрізаємо його
        $isImage = false;
        if (Str::endsWith($alt, '.png')) {
            $isImage = true;
            $alt = Str::before($alt, '.png');
        }

        // Шукаємо категорію
        $cat = DB::table('categories')->where('id', $id)->first();
        if (!$cat) {
            abort(404, 'Category not found');
        }

        // Якщо запит на PNG — віддаємо зображення
        if ($isImage) {
            $path = storage_path("app/public/collections/{$cat->id}-{$cat->alt_name}/index.png");

            if (!file_exists($path)) {
                abort(404, 'Image not found');
            }

            return response()->file($path, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=604800', // 7 днів
            ]);
        }

        // Інакше — повертаємо сторінку колекції
        return view('collection.show', compact('cat'));
    }


    // public function showCollection($name)
    // {
        
    //     $cat = categories::where('alt_name', '=', $name)->firstOrFail();
   
    //     $localPath = base_path('resources/categories/' . $cat->img);
    //     $storagePath = storage_path('app/public/' . $cat->img);

    //     if (file_exists($localPath)) {
    //         $r = file_get_contents($localPath);
    //     } elseif (file_exists($storagePath)) {
    //         $r = file_get_contents($storagePath);
    //     } else {
    //         abort(404, 'Image not found in resources or storage.');
    //     }
    
    //     $collections_folder = $this->createCollectionsImagesFolders();
    //     file_put_contents($collections_folder . '/' . $name . '.png', $r);
    
    //     return response($r, 200)->header('Content-Type', 'image/png');
    // }
    

}
