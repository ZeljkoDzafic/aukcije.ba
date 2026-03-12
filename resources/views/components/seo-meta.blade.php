@props([
    'title' => config('app.name', 'Aukcije'),
    'description' => 'Regionalna aukcijska platforma sa escrow zaštitom i real-time licitiranjem.',
    'image' => url('/og-default.jpg'),
    'type' => 'website',
])

<meta name="description" content="{{ $description }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $image }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
<link rel="canonical" href="{{ url()->current() }}">
