<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    

    public function boot() {
        date_default_timezone_set("America/New_York");
        Paginator::defaultView('vendor/pagination/bootstrap-4');

        $this->createSymlinkIfNotExists(storage_path('app/public/collection'), public_path('collection'));
        $this->createSymlinkIfNotExists(storage_path('app/public/cursors'), public_path('cursors'));
        $this->createSymlinkIfNotExists(storage_path('app/public/pointers'), public_path('pointers'));

        if (app()->environment('remote')) {
            URL::forceScheme('https');
        }
    }

    private function createSymlinkIfNotExists($target, $link)
    {
        if (File::exists($link)) {
	        if (is_file($link)) {
        	    unlink($link);
	        }
        }

	if (!file_exists($link)) {
	    symlink($target, $link);
	}
    }

}
