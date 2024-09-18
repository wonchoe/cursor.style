<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
    $currentDomain = $request->getHost();
    $domainParts = explode('.', $currentDomain);
    $domainLanguage = strtolower(substr($domainParts[0], 0, 2));

    if ($domainLanguage === 'ua') {
        $domainLanguage = 'uk';
    }

    $supportedLanguages = ['es', 'uk', 'ru'];
    $defaultLanguage = 'en';

    $language = in_array($domainLanguage, $supportedLanguages) ? $domainLanguage : $defaultLanguage;

    \App::setLocale($language);

    return $next($request);
    }
}
