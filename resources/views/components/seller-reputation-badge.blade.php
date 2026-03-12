{{--
    Seller Reputation Badge Component
    Shows seller reputation with tooltip showing detailed stats
--}}

@props(['seller', 'size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-12 h-12 text-sm',
        'lg' => 'w-16 h-16 text-base',
    ];
    
    $badgeColor = match(true) {
        $seller->trust_score >= 4.5 => 'bg-green-500',
        $seller->trust_score >= 4.0 => 'bg-blue-500',
        $seller->trust_score >= 3.5 => 'bg-yellow-500',
        $seller->trust_score >= 3.0 => 'bg-orange-500',
        default => 'bg-red-500',
    };
    
    $badgeIcon = match(true) {
        $seller->trust_score >= 4.5 => '🏆',
        $seller->trust_score >= 4.0 => '⭐',
        $seller->trust_score >= 3.5 => '✓',
        default => '⚠',
    };
@endphp

<div class="relative group" x-data="{ showTooltip: false }">
    {{-- Badge --}}
    <div 
        class="{{ $sizeClasses[$size] }} {{ $badgeColor }} rounded-full flex items-center justify-center text-white font-bold shadow-lg"
        @mouseenter="showTooltip = true"
        @mouseleave="showTooltip = false"
    >
        {{ $badgeIcon }}
    </div>
    
    {{-- Verified Badge --}}
    @if($seller->hasRole('verified_seller'))
        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-blue-500 rounded-full border-2 border-white flex items-center justify-center text-white text-xs">
            ✓
        </div>
    @endif
    
    {{-- Tooltip --}}
    <div 
        x-show="showTooltip"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 p-4 z-50"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="border-b border-gray-200 pb-2 mb-2">
            <h4 class="font-semibold text-gray-900">{{ $seller->name }}</h4>
            <p class="text-xs text-gray-500">
                {{ $seller->hasRole('verified_seller') ? 'Verifikovani prodavac' : 'Prodavac' }}
            </p>
        </div>
        
        {{-- Stats --}}
        <div class="space-y-2 text-sm">
            {{-- Trust Score --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Reputacija</span>
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($seller->trust_score))
                            <span class="text-yellow-500">★</span>
                        @else
                            <span class="text-gray-300">★</span>
                        @endif
                    @endfor
                    <span class="ml-2 font-semibold text-gray-900">{{ number_format($seller->trust_score, 1) }}</span>
                </div>
            </div>
            
            {{-- Fulfilment Rate --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Ispunjenost</span>
                <span class="font-semibold text-green-600">{{ number_format($seller->fulfilment_rate ?? 0, 0) }}%</span>
            </div>
            
            {{-- Avg Response Time --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Vrijeme odgovora</span>
                <span class="font-semibold text-gray-900">{{ $seller->avg_response_time ?? '< 1h' }}</span>
            </div>
            
            {{-- Dispute Rate --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Sporovi</span>
                <span class="font-semibold {{ ($seller->dispute_rate ?? 0) < 5 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($seller->dispute_rate ?? 0, 1) }}%
                </span>
            </div>
            
            {{-- Total Sales --}}
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Ukupno prodaja</span>
                <span class="font-semibold text-gray-900">{{ number_format($seller->total_sales ?? 0) }}</span>
            </div>
        </div>
        
        {{-- Member Since --}}
        <div class="border-t border-gray-200 mt-3 pt-3 text-xs text-gray-500 text-center">
            Član od {{ $seller->created_at->format('M Y') }}
        </div>
        
        {{-- Arrow --}}
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-3 h-3 bg-white border-r border-b border-gray-200"></div>
    </div>
</div>
