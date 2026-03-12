# Localization Guide (i18n)

## Supported Languages

The Aukcijska Platforma supports the following languages and scripts:

| Code | Language | Native Name | Script | Flag |
|------|----------|-------------|--------|------|
| `en` | English | English | Latin | 🇬🇧 |
| `bs` | Bosnian | Bosanski | Latin | 🇧🇦 |
| `sr` | Serbian | Srpski (latinica) | Latin | 🇷🇸 |
| `sr-cyrl` | Serbian | Српски (ћирилица) | Cyrillic | 🇷🇸 |
| `hr` | Croatian | Hrvatski | Latin | 🇭🇷 |
| `me` | Montenegrin | Crnogorski | Latin | 🇲🇪 |
| `mk` | Macedonian | Македонски | Cyrillic | 🇲🇰 |

## Configuration

### Default Locale

Set in `config/app.php`:

```php
'locale' => 'bs',
'fallback_locale' => 'bs',
```

### Localization Settings

Configure in `config/localization.php`:

```php
return [
    'default' => 'bs',
    'fallback' => 'bs',
    'detection' => ['session', 'url', 'browser', 'cookie', 'default'],
    'script_conversion' => [
        'enabled' => true,
        'serbian' => [
            'auto_detect' => true,
            'allow_switch' => true,
        ],
    ],
];
```

## Usage

### In Controllers

```php
use Illuminate\Support\Facades\App;

// Set locale
App::setLocale('sr-cyrl');

// Get current locale
$current = App::getLocale();
```

### In Views (Blade)

```blade
{{-- Translate text --}}
@lang('messages.nav.home')
{{ __('messages.auctions.title') }}

{{-- Translate with parameters --}}
@lang('messages.dashboard.welcome', ['name' => $user->name])

{{-- Translate array --}}
@lang("messages.status.{$status}")
```

### In PHP Code

```php
// Using trans() helper
$title = trans('messages.auctions.title');

// Using Lang facade
$title = Lang::get('messages.auctions.title');

// With parameters
$welcome = trans('messages.dashboard.welcome', ['name' => $user->name]);
```

### Language Switcher

Use the component in your layouts:

```blade
<x-language-switcher />
```

Or manually:

```blade
<div class="language-switcher">
    @foreach(available_locales() as $locale)
        <a href="?lang={{ $locale['code'] }}">
            {{ $locale['flag'] }} {{ $locale['native'] }}
        </a>
    @endforeach
</div>
```

## Helper Functions

### Locale Information

```php
locale_name()           // 'Bosnian'
locale_native()         // 'Bosanski'
locale_flag()           // '🇧🇦'
locale_script()         // 'latin' or 'cyrillic'
locale_direction()      // 'ltr' or 'rtl'
is_rtl()                // false
is_cyrillic()           // false
supported_locales()     // ['en', 'bs', 'sr', 'sr-cyrl', ...]
```

### Script Conversion

```php
// Latin to Cyrillic
transliterate('Srpski', 'cyrillic');  // 'Српски'
latinToCyrillic('Srpski');            // 'Српски'

// Cyrillic to Latin
transliterate('Српски', 'latin');     // 'Srpski'
cyrillicToLatin('Српски');            // 'Srpski'
```

### Formatting

```php
// Currency
format_currency(100.50);              // '100,50 KM'
format_currency(100.50, 'en', 'EUR'); // '€ 100.50'

// Date
format_date(now());                   // '11.03.2026.'
```

## Adding New Translations

### 1. Create Translation File

Create `lang/{locale}/messages.php`:

```php
<?php

return [
    'nav' => [
        'home' => 'Početna',
        // ...
    ],
];
```

### 2. Add Locale to Config

In `config/localization.php`:

```php
'locales' => [
    'new' => [
        'name' => 'New Language',
        'native' => 'Native Name',
        'script' => 'latin',
        'flag' => '🏳️',
        'direction' => 'ltr',
        'enabled' => true,
    ],
],
```

### 3. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## Serbian Script Switching

For Serbian, users can switch between Latin and Cyrillic:

```blade
@if(App::getLocale() === 'sr')
    <a href="?lang=sr-cyrl">Ћирилица</a>
@elseif(App::getLocale() === 'sr-cyrl')
    <a href="?lang=sr">Latinica</a>
@endif
```

## Best Practices

1. **Use translation keys** - Never hardcode text
2. **Keep keys consistent** - Use dot notation: `section.element`
3. **Use parameters** - For dynamic content
4. **Test all locales** - Verify translations work correctly
5. **Handle missing translations** - Fallback locale is used
6. **Consider text length** - Translations may vary in length
7. **RTL support** - Use `locale_direction()` for RTL languages

## Translation Files Structure

```
lang/
├── en/
│   ├── messages.php      # Main translations
│   ├── validation.php    # Validation messages
│   └── pagination.php    # Pagination links
├── bs/
│   ├── messages.php
│   ├── validation.php
│   └── pagination.php
├── sr/
│   └── ...
├── sr-cyrl/
│   └── ...
├── hr/
│   └── ...
├── me/
│   └── ...
└── mk/
    └── ...
```

## Middleware

The `SetLocale` middleware automatically:
- Detects user's preferred language
- Sets application locale
- Sets Carbon locale (for dates)
- Shares locale data with views

Add to `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\SetLocale::class,
        // ...
    ],
];
```

## Testing Translations

```php
// Test translation exists
$this->assertTranslated('messages.nav.home');

// Test all locales have translation
foreach (supported_locales() as $locale) {
    App::setLocale($locale);
    $this->assertNotEmpty(__('messages.nav.home'));
}
```
