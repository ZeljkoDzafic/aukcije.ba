<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from various sources
        $locale = $this->determineLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Set locale for Carbon (dates)
        Carbon::setLocale($locale);

        // Share locale with all views
        view()->share('currentLocale', $locale);
        view()->share('locales', $this->getAvailableLocales());

        return $next($request);
    }

    /**
     * Determine the locale from various sources
     */
    protected function determineLocale(Request $request): string
    {
        // Priority order:
        // 1. URL parameter (?lang=bs)
        // 2. Session
        // 3. Cookie
        // 4. Browser language
        // 5. Default

        // Check URL parameter
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if ($this->isSupported($locale)) {
                Session::put('locale', $locale);

                return $locale;
            }
        }

        // Check session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($this->isSupported($locale)) {
                return $locale;
            }
        }

        // Check cookie
        if ($request->cookie('locale')) {
            $locale = $request->cookie('locale');
            if ($this->isSupported($locale)) {
                Session::put('locale', $locale);

                return $locale;
            }
        }

        // Check browser language
        $browserLocale = $request->getPreferredLanguage($this->getSupportedLocales());
        if ($browserLocale && $this->isSupported($browserLocale)) {
            Session::put('locale', $browserLocale);

            return $browserLocale;
        }

        // Return default
        return config('app.locale', 'bs');
    }

    /**
     * Check if locale is supported
     */
    protected function isSupported(string $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales());
    }

    /**
     * Get supported locales
     *
     * @return list<string>
     */
    protected function getSupportedLocales(): array
    {
        /** @var array<string, array<string, mixed>> $locales */
        $locales = config('localization.locales', []);

        return array_keys(array_filter($locales, fn ($l) => $l['enabled'] ?? true));
    }

    /**
     * Get available locales for views
     *
     * @return list<array{code: string, name: mixed, native: mixed, script: mixed, flag: mixed, direction: mixed}>
     */
    protected function getAvailableLocales(): array
    {
        /** @var array<string, array<string, mixed>> $locales */
        $locales = config('localization.locales', []);
        $available = [];

        foreach ($locales as $code => $config) {
            if (($config['enabled'] ?? true) !== true) {
                continue;
            }

            $available[] = [
                'code' => $code,
                'name' => $config['name'] ?? $code,
                'native' => $config['native'] ?? $code,
                'script' => $config['script'] ?? 'latin',
                'flag' => $config['flag'] ?? '🌐',
                'direction' => $config['direction'] ?? 'ltr',
            ];
        }

        return $available;
    }
}
