@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $classes = match ($variant) {
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        'ghost' => 'btn-outline',
        default => 'btn-primary',
    };
@endphp

@if ($href)
    <a {{ $attributes->merge(['class' => $classes]) }} href="{{ $href }}">
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => $type]) }}>
        {{ $slot }}
    </button>
@endif
