<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBuildVersion extends Command
{
    protected $signature = 'custom:buildUpdate';
    protected $description = 'Update the build version number in build.txt by +1';

    public function handle()
    {
        $path = base_path('build.txt');
        $version = 1;

        if (file_exists($path)) {
            $content = trim(file_get_contents($path));
            if (is_numeric($content)) {
                $version = (int)$content + 1;
            }
        }

        file_put_contents($path, $version);
        $this->info("✅ Build version updated to: {$version}");
    }
}
