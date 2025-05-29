<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Cursors;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class ImageController extends Controller {

    // OLD IMAGE ROUTE
    public function show($type, $category, $name)
    {
        // Перевіряємо, чи $type виглядає як c-1234 або p-5678
        if (preg_match('/^(c|p)-(\d+)$/', $type, $m)) {
            $typeLetter = $m[1];    // "c" або "p"
            $id = $m[2];            // числовий id

            // Формуємо новий URL
            $cursorType = ($typeLetter === 'c') ? 'cursor' : 'pointer';
            $newUrl = "/collections/0-any/{$id}-any-{$cursorType}.svg";

            // 301 редірект
            return redirect($newUrl, 301);
        }

        // Якщо не співпало — віддаємо 404
        abort(404);
    }

    public function serveWebp($collection, $file)
    {
        $pngPath = public_path("collections/$collection/{$file}.png");
        $webpPath = public_path("collections/$collection/{$file}.webp");

        // Якщо вже є webp
        if (file_exists($webpPath)) {
            return response()->file($webpPath, ['Content-Type' => 'image/webp']);
        }

        // Якщо немає png, 404
        if (!file_exists($pngPath)) {
            abort(404);
        }

        // Генеруємо webp через Intervention Image
        $image = Image::make($pngPath)->encode('webp', 90);
        $image->save($webpPath);

        return response($image, 200)->header('Content-Type', 'image/webp');
    }
    
    public function serveImage($category_slug, $cursor_slug)
    {

        $target = storage_path('app/public/collections'); // абсолютний шлях
        $link = public_path('collections');

        if (!is_link($link)) {
            symlink($target, $link);
        }

        // Обрізаємо .svg якщо є
        if (Str::endsWith($cursor_slug, '.svg')) {
            $cursor_slug = Str::beforeLast($cursor_slug, '.svg');
        }

        $parts = explode('-', $cursor_slug);
        if (count($parts) < 2) {
            abort(404);
        }

        $type = array_pop($parts); // cursor або pointer
        $id = $parts[0];

        $cursor = Cursors::where('id', $id)->firstOrFail();

        switch ($type) {
            case 'cursor':
                $filePath = $cursor->c_file;
                break;
            case 'pointer':
                $filePath = $cursor->p_file;
                break;
            default:
                abort(404);
        }

        $full_path = Storage::disk('public')->path($filePath);

        if (!file_exists($full_path)) {
            abort(404);
        }
    
        $response = response()->file($full_path, [
            'Content-Type' => 'image/svg+xml',
        ]);          
        $response->headers->set('Cache-Tag', 'svg');
        return $response;
    }

}
