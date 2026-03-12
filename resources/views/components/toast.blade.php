@props(['variant' => 'info', 'message' => ''])

@php
    $classes = match ($variant) {
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        default => 'alert-info',
    };
@endphp

<div {{ $attributes->merge(['class' => "toast-item {$classes}"]) }}>
    {{ $message ?: $slot }}
</div>
