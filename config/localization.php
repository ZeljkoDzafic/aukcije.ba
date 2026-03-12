<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of supported locales with their configurations.
    | Each locale has: name, native name, script, flag, direction
    |
    */

    'locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'script' => 'latin',
            'flag' => '🇬🇧',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'bs' => [
            'name' => 'Bosnian',
            'native' => 'Bosanski',
            'script' => 'latin',
            'flag' => '🇧🇦',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'sr' => [
            'name' => 'Serbian (Latin)',
            'native' => 'Srpski (latinica)',
            'script' => 'latin',
            'flag' => '🇷🇸',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'sr-cyrl' => [
            'name' => 'Serbian (Cyrillic)',
            'native' => 'Српски (ћирилица)',
            'script' => 'cyrillic',
            'flag' => '🇷🇸',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'hr' => [
            'name' => 'Croatian',
            'native' => 'Hrvatski',
            'script' => 'latin',
            'flag' => '🇭🇷',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'me' => [
            'name' => 'Montenegrin',
            'native' => 'Crnogorski',
            'script' => 'latin',
            'flag' => '🇲🇪',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'mk' => [
            'name' => 'Macedonian',
            'native' => 'Македонски',
            'script' => 'cyrillic',
            'flag' => '🇲🇰',
            'direction' => 'ltr',
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale for the application.
    |
    */

    'default' => 'bs',

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale used when translation is not available.
    |
    */

    'fallback' => 'bs',

    /*
    |--------------------------------------------------------------------------
    | Locale Detection
    |--------------------------------------------------------------------------
    |
    | Methods for detecting user's preferred locale.
    | Order matters - first match wins.
    |
    */

    'detection' => [
        'session',      // User's session preference
        'url',          // URL prefix (/bs/, /sr/, etc.)
        'browser',      // Browser language
        'cookie',       // Cookie preference
        'default',      // Default locale
    ],

    /*
    |--------------------------------------------------------------------------
    | Script Conversion
    |--------------------------------------------------------------------------
    |
    | Enable automatic script conversion for Serbian.
    |
    */

    'script_conversion' => [
        'enabled' => true,
        'serbian' => [
            'auto_detect' => true,
            'allow_switch' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Caching
    |--------------------------------------------------------------------------
    |
    | Cache translations for better performance.
    |
    */

    'cache' => [
        'enabled' => true,
        'duration' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Paths
    |--------------------------------------------------------------------------
    |
    | Additional paths to scan for translations.
    |
    */

    'paths' => [
        resource_path('lang'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Missing Translation Handling
    |--------------------------------------------------------------------------
    |
    | What to do when translation is missing.
    | Options: 'fallback', 'key', 'empty'
    |
    */

    'missing' => 'fallback',

    /*
    |--------------------------------------------------------------------------
    | Translation Editor
    |--------------------------------------------------------------------------
    |
    | Allow editing translations via UI (admin only).
    |
    */

    'editor' => [
        'enabled' => false,
        'roles' => ['admin'],
    ],

];
