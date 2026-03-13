{{--
    Similar Auctions Section Component
    Shows related auctions based on category
--}}

@props([
    'currentAuction' => null,
    'categoryId' => null,
    'limit' => 4,
])

@php
    $similarAuctions = \App\Models\Auction::query()
        ->active()
        ->where('category_id', $currentAuction->category_id ?? $categoryId)
        ->where('id', '!=', $currentAuction->id ?? 0)
        ->orderBy('ends_at')
        ->limit($limit)
        ->with(['seller', 'primaryImage'])
        ->get();
@endphp

@php
    $currentCategory = $currentAuction && property_exists($currentAuction, 'category') ? $currentAuction->category : null;
@endphp

@if($similarAuctions->count() > 0)
    <section class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Slične aukcije
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($similarAuctions as $auction)
                <a 
                    href="{{ route('auctions.show', $auction) }}"
                    class="block group"
                >
                    <div class="card-hover overflow-hidden">
                        {{-- Image --}}
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            @if($auction->primaryImage)
                                <img
                                    src="{{ $auction->primaryImage->url }}"
                                    alt="{{ $auction->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $auction->title }}
                            </h3>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">Trenutna cijena</p>
                                    <p class="text-lg font-bold text-blue-600">
                                        {{ number_format($auction->current_price, 2) }} KM
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Time Remaining --}}
                            <div class="mt-3 flex items-center text-sm {{ $auction->is_ending_soon ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $auction->time_remaining }}
                            </div>
                            
                            {{-- Seller --}}
                            <div class="mt-3 flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $auction->seller->name }}
                                @if($auction->seller->hasRole('verified_seller'))
                                    <span class="ml-1 text-blue-500">✓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        {{-- View All Link --}}
        @if($currentCategory)
            <div class="mt-6 text-center">
                <a 
                    href="{{ route('categories.show', $currentCategory) }}"
                    class="btn-outline inline-flex items-center text-blue-600 hover:text-blue-700 font-medium"
                >
                    Pogledaj sve iz kategorije "{{ $currentCategory->name }}"
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif
    </section>
@endif
