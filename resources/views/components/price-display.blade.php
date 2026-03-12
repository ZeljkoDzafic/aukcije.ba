@props(['amount' => '0.00', 'currency' => 'BAM'])

<div class="space-y-1">
    <p class="price-label">{{ $slot->isNotEmpty() ? $slot : 'Trenutna cijena' }}</p>
    <p class="price">{{ number_format((float) $amount, 2, ',', '.') }} {{ $currency }}</p>
</div>
