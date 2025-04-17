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

        $response = $next($request);

        if (str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set('Content-Language', App::getLocale());
        }

        return $response;

    }
}
