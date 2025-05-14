<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixCategoryImages extends Command
{
    protected $signature = 'fix:category-images';
    protected $description = 'Створює index.png для кожної категорії в collections/{id}-{alt_name} і оновлює шлях у БД';

    public function handle()
    {
        $categories = DB::table('categories')->get();

        $baseStorage = storage_path('app/public');
        $collectionsDir = $baseStorage . '/collections';
        $collectionSrcDir = $baseStorage . '/collection';
        $defaultCatPath = 'categories/';

        foreach ($categories as $cat) {
            $catId = $cat->id;
            $alt = $cat->alt_name;
            $folder = "$collectionsDir/{$catId}-{$alt}";
            File::ensureDirectoryExists($folder);

            $img = $cat->img;
            $target = "$folder/index.png";

            if (Str::startsWith($img, $defaultCatPath)) {
                $filename = Str::after($img, $defaultCatPath);
                $source = storage_path("app/public/categories/{$filename}");

                if (file_exists($source)) {
                    File::copy($source, $target);
                    if (!file_exists($target)) {
                        $this->error("❌ Помилка: не вдалося створити файл index.png для ID {$catId}");
                        continue;
                    }
                    DB::table('categories')->where('id', $catId)->update([
                        'img' => "collections/{$catId}-{$alt}/index.png"
                    ]);
                    $this->info("✔ Copied from public/categories for ID {$catId}");
                } else {
                    $this->warn("⚠ Файл не знайдено в public/categories для ID {$catId}: {$filename}");
                }
            } else {
                $source = "$collectionSrcDir/{$alt}.png";
                if (file_exists($source)) {
                    File::copy($source, $target);
                    if (!file_exists($target)) {
                        $this->error("❌ Помилка: не вдалося створити файл index.png для ID {$catId}");
                        continue;
                    }
                    DB::table('categories')->where('id', $catId)->update([
                        'img' => "collections/{$catId}-{$alt}/index.png"
                    ]);
                    $this->info("✔ Copied from collection/ for ID {$catId}");
                } else {
                    $this->warn("⚠ Файл не знайдено в collection/ для ID {$catId}: {$alt}.png");
                }
            }
        }

        $this->info('✅ Завершено.');
    }
}
