<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - ' . config('app.name', 'Aukcije'))</title>

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
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Admin Sidebar --}}
        @include('layouts.partials.admin-sidebar')

        {{-- Main Content Area --}}
        <div class="flex flex-1 flex-col overflow-hidden">
            {{-- Admin Header --}}
            @include('layouts.partials.admin-header')

            {{-- Main Content --}}
            <main id="main-content" class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Toast Notifications --}}
    @include('layouts.partials.toast')

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
