{{--
    Reserve Price Badge Component
    Shows reserve price status on auction
--}}

@props(['auction'])

@php
    $reserveMet = $auction->reserve_price && $auction->current_price >= $auction->reserve_price;
    $reserveNotMet = $auction->reserve_price && $auction->current_price < $auction->reserve_price;
@endphp

@if($auction->reserve_price)
    <div 
        @if($reserveNotMet)
            x-data="{ showTooltip: false }"
            class="inline-flex items-center"
        @endif
    >
        @if($reserveMet)
            {{-- Reserve Met Badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Rezervna cijena dostignuta
            </span>
        @elseif($reserveNotMet)
            {{-- Reserve Not Met Badge --}}
            <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 cursor-help"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false"
            >
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                ???
            </span>
            
            {{-- Tooltip --}}
            <div 
                x-show="showTooltip"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-50 mt-2 w-64 p-3 bg-gray-900 text-white text-sm rounded-lg shadow-lg"
                style="display: none;"
            >
                Rezervna cijena nije dostignuta.
                <div class="mt-1 text-xs text-gray-300">
                    Minimalna ponuda mora biti {{ number_format($auction->reserve_price, 2) }} KM ili više.
                </div>
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
            </div>
        @endif
    </div>
@endif
