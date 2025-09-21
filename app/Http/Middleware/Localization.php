<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Localization
{
    /**
     * Default locale if no other locale is determined
     */
    // private const DEFAULT_LOCALE = ;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Starting localization middleware');

        $locale = $this->determineLocale($request);
        $this->setLocale($locale);

        $response = $next($request);

        return $this->addLocaleHeader($response, $locale);
    }

    /**
     * Determine the locale based on priority:
     * 1. Request header
     * 2. Authenticated applicant preference
     * 3. Default locale
     */
    private function determineLocale(Request $request): string
    {
        $supportedLocales = $this->getSupportedLocales();

        return $this->getLocaleFromHeader($request)
            ?? $this->getLocaleFromMember($request)
            // ?? self::DEFAULT_LOCALE;
            ?? config('app.locale');
    }

    /**
     * Get locale from request header
     */
    private function getLocaleFromHeader(Request $request): ?string
    {
        if (! $request->hasHeader('Content-Language')) {
            Log::info('No Content-Language header found');

            return null;
        }

        $headerLocale = strtolower($request->header('Content-Language'));

        if (! $this->isValidLocale($headerLocale)) {
            Log::info("Invalid Content-Language header: {$headerLocale}");

            return null;
        }

        Log::info("Using locale from header: {$headerLocale}");

        return $headerLocale;
    }

    /**
     * Get locale from authenticated member
     */
    private function getLocaleFromMember(Request $request): ?string
    {
        $member = $request->user('member');

        if (! $member) {
            Log::info('No authenticated member found');

            return null;
        }

        $memberLocale = $member->preferred_language;

        if (! $this->isValidLocale($memberLocale)) {
            Log::info("Invalid member preferred language: {$memberLocale}");

            return null;
        }

        Log::info("Using member's preferred language: {$memberLocale}");

        return $memberLocale;
    }

    /**
     * Get supported locales from config
     */
    private function getSupportedLocales(): array
    {
        return config('app.locales', ['en']);
    }

    /**
     * Check if the locale is valid
     */
    private function isValidLocale(?string $locale): bool
    {
        if (! $locale) {
            return false;
        }

        return in_array($locale, $this->getSupportedLocales());
    }

    /**
     * Set the application locale
     */
    private function setLocale(string $locale): void
    {
        app()->setLocale($locale);
        Log::info("Application locale set to: {$locale}");
    }

    /**
     * Add locale to response headers
     */
    private function addLocaleHeader($response, string $locale)
    {
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
        }

        return $response;
    }
}
