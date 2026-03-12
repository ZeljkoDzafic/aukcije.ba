@props([
    'name' => 'Aukcije User',
    'size' => 'md',
])

@php
    $dimension = match ($size) {
        'sm' => 'h-10 w-10',
        'lg' => 'h-16 w-16',
        default => 'h-12 w-12',
    };

    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<span {{ $attributes->merge(['class' => "inline-flex {$dimension} items-center justify-center rounded-full bg-trust-100 font-semibold text-trust-700"]) }}>
    {{ $initials ?: 'AU' }}
</span>
