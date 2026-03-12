@props(['variant' => 'info'])

@php
    $classes = match ($variant) {
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'trust' => 'badge-trust',
        default => 'badge-info',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
