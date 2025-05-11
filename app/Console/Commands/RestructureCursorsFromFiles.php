<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\cursor;

class RestructureCursorsFromFiles extends Command
{
    protected $signature = 'cursors:restructure-from-files';
    protected $description = 'Organize cursors and pointers from multiple source folders into structured collections';

    public function handle()
    {
        $cursors = cursor::with('categories')->get();

        foreach ($cursors as $item) {
            $this->processItem($item, 'cursor', $item->c_file);
            $this->processItem($item, 'pointer', $item->p_file);
        }

        $this->info("🎉 All done: cursors and pointers reorganized.");
    }

    private function processItem($item, string $type, ?string $originalFileName)
    {
        if (!$originalFileName) {
            $this->warn("⚠️ No file name defined for ID {$item->id} ({$type})");
            return;
        }

        $category = $item->categories;
        if (!$category) {
            $this->warn("⚠️ No category found for ID {$item->id}");
            return;
        }

        $slugName = Str::slug($item->name);
        if (empty($slugName)) {
            $slugName = 'unnamed';
            $this->warn("⚠️ Empty slug name for ID {$item->id}, fallback to 'unnamed'");
        }

        $catId = $category->id;
        $catAlt = $category->alt_name;

        $sourcePaths = [
            "public/resources/{$type}s/{$originalFileName}",
            "public/{$type}s_new/{$originalFileName}",
            "public/{$type}s/{$originalFileName}",
            "public/{$type}s/{$catId}-{$catAlt}/{$originalFileName}",
        ];

        $sourcePath = null;
        foreach ($sourcePaths as $tryPath) {
            if (Str::startsWith($tryPath, 'public/resources')) {
                $fullPath = public_path(Str::replaceFirst('public/', '', $tryPath));
                if (file_exists($fullPath)) {
                    $sourcePath = $fullPath;
                    break;
                }
            } elseif (Storage::exists($tryPath)) {
                $sourcePath = Storage::path($tryPath);
                break;
            }
        }

        if (!$sourcePath) {
            $this->warn("❌ File not found in any folder for ID {$item->id} → {$originalFileName}");
            return;
        }

        $collectionFolder = "public/collections/{$catId}-{$catAlt}";
        Storage::makeDirectory($collectionFolder);

        $newName = "{$item->id}-{$slugName}-{$type}.svg";
        $newPath = "$collectionFolder/$newName";

        if (Storage::exists($newPath)) {
            $this->warn("⚠️ Already exists: $newPath");
            return;
        }

        copy($sourcePath, storage_path("app/{$newPath}"));
        $relativePath = Str::replaceFirst('public/', '', $newPath);

        if ($type === 'cursor') {
            $item->c_file = $relativePath;
        } else {
            $item->p_file = $relativePath;
        }

        $item->save();

        $this->info("✅ Copied and renamed: {$originalFileName} → $relativePath");
    }
}
