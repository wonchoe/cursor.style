<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class SetLocaleBySubdomain
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost(); // напр. ko.cursor.style
        $prefix = explode('.', $host)[0]; // ko

        $localeFolder = $prefix === 'en' ? 'en_gb' : $prefix;

        if (File::exists(resource_path("lang/{$localeFolder}"))) {
            App::setLocale($localeFolder);
        }

        return $next($request);
    }
}
