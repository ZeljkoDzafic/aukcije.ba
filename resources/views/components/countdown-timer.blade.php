@props(['time' => '02d 11h 24m', 'tone' => 'normal'])

@php
    $class = match ($tone) {
        'urgent' => 'countdown-urgent',
        'warning' => 'countdown-warning',
        default => 'countdown-normal',
    };
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>{{ $time }}</span>
