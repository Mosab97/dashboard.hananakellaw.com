<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LanguageSwitcherController extends Controller
{
    public function setLanguage(Request $request, $language)
    {
        app()->setLocale($language);

        if (str_starts_with($request->path(), 'dashboard')) {
            // Set dashboard language
            Cookie::queue('dashboard_language', $language, 60 * 24 * 365);
        } else {
            // Set website language
            Cookie::queue('website_language', $language, 60 * 24 * 365);
        }

        return redirect()->back();
    }

    // Optional: Add separate methods for clarity
    public function setDashboardLanguage(Request $request, $language)
    {
        app()->setLocale($language);
        Cookie::queue('dashboard_language', $language, 60 * 24 * 365);

        return redirect()->back();
    }

    public function setWebsiteLanguage(Request $request, $language)
    {
        app()->setLocale($language);
        Cookie::queue('website_language', $language, 60 * 24 * 365);

        return redirect()->back();
    }
}
