<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Illuminate\Pagination\Paginator;
use Request;
use Illuminate\Http\Request as rqst;
use Redirect;
use Cookie;

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
    }

}
