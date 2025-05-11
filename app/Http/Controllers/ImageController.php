<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cursor;
use App\Models\categories;

class ImageController extends Controller {

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

    public function getSvg(Request $r) {
        $cur_cursor = cursor::findOrFail($r->id);
    
        if (($r->type !== 'cursors') && ($r->type !== 'pointers'))
            abort(404);
    
        if ($r->type == 'cursors') {
            $get = $cur_cursor->c_file;
        } else {
            $get = $cur_cursor->p_file;
        }
    
        $basePath = public_path("resources/{$r->type}/{$get}");
    
        // Якщо не існує, пробуємо зі storage
        if (!file_exists($basePath)) {
            $basePath = storage_path("app/public/{$r->type}_new/{$get}");
            if (!file_exists($basePath)) {
                abort(404, 'SVG file not found.');
            }
        }
    
        $file = file_get_contents($basePath);
    
        $search_from = round(strlen($file) / 2);
        $find = intval(strpos($file, '<path', $search_from));
        $full_str = $file;
    
        if ($find) {
            $part_one = substr($file, 0, $find);
            $part_two = substr($file, $find);
            $copyright = '<desc>cursor-style.com</desc>';
            $full_str = $part_one . $copyright . $part_two;
        }
    
        $cursors_folder = $this->createCursorsImagesFolders();
        $pointers_folder = $this->createPointersImagesFolders();
    
        if ($r->type == 'cursors') {
            file_put_contents("{$cursors_folder}/{$r->id}-{$r->cursor}.svg", $full_str);
        } else {
            file_put_contents("{$pointers_folder}/{$r->id}-{$r->cursor}.svg", $full_str);
        }
    
        return response($full_str, 200)->header('Content-Type', 'image/svg+xml');
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
    

    public function showCollection($name)
    {
        
        $cat = categories::where('alt_name', '=', $name)->firstOrFail();
   
        $localPath = base_path('resources/categories/' . $cat->img);
        $storagePath = storage_path('app/public/' . $cat->img);

        if (file_exists($localPath)) {
            $r = file_get_contents($localPath);
        } elseif (file_exists($storagePath)) {
            $r = file_get_contents($storagePath);
        } else {
            abort(404, 'Image not found in resources or storage.');
        }
    
        $collections_folder = $this->createCollectionsImagesFolders();
        file_put_contents($collections_folder . '/' . $name . '.png', $r);
    
        return response($r, 200)->header('Content-Type', 'image/png');
    }
    

}
