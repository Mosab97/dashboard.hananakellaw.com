<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->path(), str_starts_with($request->path(), 'dashboard'));
        if (str_starts_with($request->path(), 'dashboard')) {
            // Dashboard language handling
            $dashboardLanguage = $request->cookie('dashboard_language');
            // dd($dashboardLanguage);

            if (empty($dashboardLanguage)) {
                app()->setLocale(config('app.locale'));
                Cookie::queue('dashboard_language', config('app.locale'), 60 * 24 * 365);
            } else {
                app()->setLocale($dashboardLanguage);
            }
        } else {
            // Website language handling
            $websiteLanguage = $request->cookie('website_language');
            if (! empty($websiteLanguage)) {
                app()->setLocale($websiteLanguage);
            }
        }

        return $next($request);
    }
}
