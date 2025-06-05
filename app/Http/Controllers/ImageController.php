<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Cursors;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Imagick;
use ImagickPixel;

class ImageController extends Controller {

    public function serveThumbnail($collection_slug, $filename)
    {
        $svgPath = storage_path("app/public/collections/{$collection_slug}/{$filename}.svg");
        $thumbDir = storage_path("app/public/collections/{$collection_slug}/thumbs");
        $pngPath = "{$thumbDir}/{$filename}.png";

        if (file_exists($pngPath)) {
            return response()->file($pngPath);
        }

        if (!file_exists($svgPath)) {
            abort(404, 'SVG not found.');
        }

        $svgContent = file_get_contents($svgPath);

        // Витягуємо base64 PNG з href, якщо є
        if (preg_match('/href="data:image\/png;base64,([^"]+)"/', $svgContent, $match)) {
            $pngData = base64_decode($match[1]);
        } else {
            // Якщо немає вбудованого PNG — конвертуємо SVG у PNG напряму
            $pngData = $this->svgToPng($svgContent, 300, 300);
            if (!$pngData) {
                abort(500, 'SVG to PNG conversion failed.');
            }
        }

        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($pngData);
            $imagick->setImageBackgroundColor(new \ImagickPixel('transparent'));
            $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
            $imagick->setImageFormat('png');
            $imagick->thumbnailImage(300, 300, true);

            $thumbWidth = $imagick->getImageWidth();
            $thumbHeight = $imagick->getImageHeight();

            $canvas = new \Imagick();
            $canvas->newImage(300, 300, new \ImagickPixel('transparent'), 'png');
            $x = (300 - $thumbWidth) / 2;
            $y = (300 - $thumbHeight) / 2;
            $canvas->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, $x, $y);
            $canvas->unsharpMaskImage(1, 0.5, 1, 0.05);

            if (!\Illuminate\Support\Facades\File::exists($thumbDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($thumbDir, 0755, true);
            }

            $canvas->writeImage($pngPath);
            $canvas->clear();
            $canvas->destroy();
            $imagick->clear();
            $imagick->destroy();
            chmod($pngPath, 0664);

        } catch (\Throwable $e) {
            \Log::error('File download error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(500, 'File error: ' . $e->getMessage());
        }

        $response = response()->file($pngPath);
        $response->headers->set('Cache-Tag', 'thumb');
        return $response;
    }

    /**
     * Конвертує SVG string в PNG (як blob)
     */
    protected function svgToPng(string $svg, int $width = 300, int $height = 300): ?string
    {
        try {
            $svg = preg_replace('/\swidth="[^"]*"/i', '', $svg);
            $svg = preg_replace('/\sheight="[^"]*"/i', '', $svg);
            $svg = preg_replace("/\swidth='[^']*'/i", '', $svg);
            $svg = preg_replace("/\sheight='[^']*'/i", '', $svg);

            // Додаємо width/height у <svg ...>
            $svg = preg_replace(
                '/<svg\b([^>]*)>/i',
                '<svg$1 width="' . $width . 'px" height="' . $height . 'px">',
                $svg,
                1
            );

            $im = new \Imagick();
            $im->setBackgroundColor(new \ImagickPixel('transparent'));
            $im->setResolution(300, 300);
            $im->readImageBlob($svg);
            $im->setImageFormat('png');
            $im->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
            $pngData = $im->getImageBlob();
            $im->clear();
            $im->destroy();
            return $pngData;
        } catch (\Throwable $e) {
            \Log::error('SVG to PNG conversion failed', ['msg' => $e->getMessage()]);
            return null;
        }
    }




    
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

        $manager = new ImageManager(new Driver());
        $manager = new ImageManager(new Driver());
        $image = $manager->read($pngPath);

        // Зменшуємо до 450px по ширині (висота пропорційно)
        $image->scaleDown(width: 450);

        $image = $image->toWebp(85);
        $image->save($webpPath);

        return response($image->toString(), 200)->header('Content-Type', 'image/webp');

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
