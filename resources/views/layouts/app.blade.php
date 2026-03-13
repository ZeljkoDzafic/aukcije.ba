<!DOCTYPE html>
<html lang="sr-Latn-BA">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Aukcije'))</title>

    {{-- SEO Meta Tags --}}
    @yield('meta')

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Scripts --}}
    @stack('scripts')

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Aukcije">
</head>
<body class="min-h-screen bg-gray-50">
    {{-- Skip to main content for accessibility --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:rounded-lg focus:shadow-lg">
        Preskoči na glavni sadržaj
    </a>

    {{-- Header --}}
    @include('layouts.partials.header')

    {{-- Main Content --}}
    <main id="main-content" class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

    {{-- Cookie Consent --}}
    <x-cookie-consent-banner />

    <div
        x-data="pwaInstallPrompt()"
        x-show="deferredPrompt && !dismissed"
        x-transition
        class="fixed inset-x-4 bottom-4 z-40 mx-auto max-w-md rounded-3xl border border-slate-200 bg-white/95 p-5 shadow-xl backdrop-blur"
        style="display: none;"
    >
        <div class="space-y-3">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Aplikacija</p>
            <h2 class="text-lg font-semibold text-slate-900">Instaliraj Aukcije.ba</h2>
            <p class="text-sm text-slate-600">Dodaj platformu na početni ekran radi bržeg pristupa, push obavještenja i stabilnijeg mobilnog iskustva.</p>
            <div class="flex gap-3">
                <button type="button" class="btn-primary flex-1" @click="install">Instaliraj</button>
                <button type="button" class="btn-secondary flex-1" @click="dismiss">Kasnije</button>
            </div>
        </div>
    </div>

    {{-- Toast Notifications --}}
    @include('layouts.partials.toast')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Vue Components --}}
    @vite(['resources/vue/app.js'])

</body>
</html>
