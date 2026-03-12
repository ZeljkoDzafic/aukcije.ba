<?php

if (!function_exists('locale_name')) {
    /**
     * Get locale name
     */
    function locale_name(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("localization.locales.{$locale}.name", $locale);
    }
}

if (!function_exists('locale_native')) {
    /**
     * Get locale native name
     */
    function locale_native(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("localization.locales.{$locale}.native", $locale);
    }
}

if (!function_exists('locale_flag')) {
    /**
     * Get locale flag emoji
     */
    function locale_flag(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("localization.locales.{$locale}.flag", '🌐');
    }
}

if (!function_exists('locale_script')) {
    /**
     * Get locale script (latin/cyrillic)
     */
    function locale_script(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("localization.locales.{$locale}.script", 'latin');
    }
}

if (!function_exists('locale_direction')) {
    /**
     * Get locale text direction
     */
    function locale_direction(?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("localization.locales.{$locale}.direction", 'ltr');
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current locale is RTL
     */
    function is_rtl(): bool
    {
        return locale_direction() === 'rtl';
    }
}

if (!function_exists('is_cyrillic')) {
    /**
     * Check if current locale uses Cyrillic script
     */
    function is_cyrillic(): bool
    {
        return locale_script() === 'cyrillic';
    }
}

if (!function_exists('supported_locales')) {
    /**
     * Get all supported locales
     */
    function supported_locales(): array
    {
        return array_keys(
            array_filter(
                config('localization.locales', []),
                fn($l) => $l['enabled'] ?? true
            )
        );
    }
}

if (!function_exists('available_locales')) {
    /**
     * Get available locales with details
     */
    function available_locales(): array
    {
        return collect(config('localization.locales', []))
            ->filter(fn($l) => $l['enabled'] ?? true)
            ->map(fn($config, $code) => array_merge($config, ['code' => $code]))
            ->values()
            ->toArray();
    }
}

if (!function_exists('set_locale')) {
    /**
     * Set locale for current session
     */
    function set_locale(string $locale): bool
    {
        if (!in_array($locale, supported_locales())) {
            return false;
        }

        \Illuminate\Support\Facades\App::setLocale($locale);
        \Illuminate\Support\Facades\Session::put('locale', $locale);
        
        return true;
    }
}

if (!function_exists('transliterate')) {
    /**
     * Transliterate text between scripts
     */
    function transliterate(string $text, string $to = 'latin'): string
    {
        if ($to === 'cyrillic') {
            return latinToCyrillic($text);
        }
        return cyrillicToLatin($text);
    }
}

if (!function_exists('latinToCyrillic')) {
    /**
     * Convert Latin to Cyrillic (Serbian)
     */
    function latinToCyrillic(string $text): string
    {
        $search = [
            'a', 'b', 'v', 'g', 'd', 'đ', 'e', 'ž', 'z', 'i', 'j', 'k', 'l', 'lj', 'm',
            'n', 'nj', 'o', 'p', 'r', 's', 't', 'ć', 'u', 'f', 'h', 'c', 'č', 'dž', 'š',
            'A', 'B', 'V', 'G', 'D', 'Đ', 'E', 'Ž', 'Z', 'I', 'J', 'K', 'L', 'Lj', 'M',
            'N', 'Nj', 'O', 'P', 'R', 'S', 'T', 'Ć', 'U', 'F', 'H', 'C', 'Č', 'Dž', 'Š'
        ];
        
        $replace = [
            'а', 'б', 'в', 'г', 'д', 'ђ', 'е', 'ж', 'з', 'и', 'ј', 'к', 'л', 'љ', 'м',
            'н', 'њ', 'о', 'п', 'р', 'с', 'т', 'ћ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш',
            'А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М',
            'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш'
        ];

        return str_replace($search, $replace, $text);
    }
}

if (!function_exists('cyrillicToLatin')) {
    /**
     * Convert Cyrillic to Latin (Serbian)
     */
    function cyrillicToLatin(string $text): string
    {
        $search = [
            'а', 'б', 'в', 'г', 'д', 'ђ', 'е', 'ж', 'з', 'и', 'ј', 'к', 'л', 'љ', 'м',
            'н', 'њ', 'о', 'п', 'р', 'с', 'т', 'ћ', 'у', 'ф', 'х', 'ц', 'ч', 'џ', 'ш',
            'А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М',
            'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш'
        ];
        
        $replace = [
            'a', 'b', 'v', 'g', 'd', 'đ', 'e', 'ž', 'z', 'i', 'j', 'k', 'l', 'lj', 'm',
            'n', 'nj', 'o', 'p', 'r', 's', 't', 'ć', 'u', 'f', 'h', 'c', 'č', 'dž', 'š',
            'A', 'B', 'V', 'G', 'D', 'Đ', 'E', 'Ž', 'Z', 'I', 'J', 'K', 'L', 'Lj', 'M',
            'N', 'Nj', 'O', 'P', 'R', 'S', 'T', 'Ć', 'U', 'F', 'H', 'C', 'Č', 'Dž', 'Š'
        ];

        return str_replace($search, $replace, $text);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency based on locale
     */
    function format_currency(float $amount, ?string $locale = null, ?string $currency = null): string
    {
        $locale = $locale ?? App::getLocale();
        $currency = $currency ?? config('payment.primary_currency', 'BAM');
        
        $formats = [
            'BAM' => ['symbol' => 'KM', 'position' => 'after'],
            'EUR' => ['symbol' => '€', 'position' => 'after'],
            'USD' => ['symbol' => '$', 'position' => 'before'],
            'RSD' => ['symbol' => 'RSD', 'position' => 'after'],
        ];
        
        $format = $formats[$currency] ?? ['symbol' => $currency, 'position' => 'after'];
        
        $formatted = number_format($amount, 2, ',', '.');
        
        return $format['position'] === 'before' 
            ? "{$format['symbol']} {$formatted}"
            : "{$formatted} {$format['symbol']}";
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date based on locale
     */
    function format_date($date, string $format = 'long'): string
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        
        return $date->format('d.m.Y.');
    }
}
