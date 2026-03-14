<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Seller - ' . config('app.name', 'Aukcije'))</title>
    @yield('meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('scripts')
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.10),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(14,165,233,0.10),_transparent_30%),linear-gradient(180deg,_#f8fafc_0%,_#fdfdf8_50%,_#f8fafc_100%)]">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
        Preskoči na glavni sadržaj
    </a>

    <div class="flex min-h-screen">
        @include('layouts.partials.seller-sidebar')

        <div class="flex min-h-screen flex-1 flex-col">
            @include('layouts.partials.seller-header')

            <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @include('layouts.partials.toast')
    @livewireScripts
</body>
</html>
