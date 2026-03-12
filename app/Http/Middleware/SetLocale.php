<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Get locale from various sources
        $locale = $this->determineLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Set locale for Carbon (dates)
        \Carbon\Carbon::setLocale($locale);

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
     */
    protected function getSupportedLocales(): array
    {
        $locales = config('localization.locales', []);
        return array_keys(array_filter($locales, fn($l) => $l['enabled'] ?? true));
    }

    /**
     * Get available locales for views
     */
    protected function getAvailableLocales(): array
    {
        return collect(config('localization.locales', []))
            ->filter(fn($l) => $l['enabled'] ?? true)
            ->map(fn($config, $code) => [
                'code' => $code,
                'name' => $config['name'],
                'native' => $config['native'],
                'script' => $config['script'],
                'flag' => $config['flag'],
                'direction' => $config['direction'],
            ])
            ->values()
            ->toArray();
    }
}
