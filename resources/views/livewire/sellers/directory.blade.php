{{--
    Seller Directory Component
    T-1252: Javna stranica /sellers: profili, filter po kategoriji, sort po reputaciji
--}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            Naši prodavci
        </h1>
        <p class="text-gray-600">
            Pronađite pouzdane prodavce i pregledajte njihove aukcije
        </p>
    </div>
    
    {{-- Filters --}}
    <div class="card mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div>
                <label class="label" for="search">Pretraži prodavce</label>
                <input
                    type="text"
                    id="search"
                    wire:model.debounce.300ms="search"
                    class="input"
                    placeholder="Ime prodavca..."
                />
            </div>
            
            {{-- Category Filter --}}
            <div>
                <label class="label" for="category">Kategorija</label>
                <select id="category" wire:model="category" class="input">
                    <option value="">Sve kategorije</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Sort --}}
            <div>
                <label class="label" for="sort">Sortiraj po</label>
                <select id="sort" wire:model="sort" class="input">
                    <option value="reputation">Reputaciji</option>
                    <option value="sales">Broju prodaja</option>
                    <option value="newest">Najnoviji</option>
                    <option value="alphabetical">Abecedi</option>
                </select>
            </div>
            
            {{-- Verified Only --}}
            <div class="flex items-end">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="verifiedOnly"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Samo verifikovani</span>
                </label>
            </div>
        </div>
    </div>
    
    {{-- Results Count --}}
    <div class="mb-6 text-sm text-gray-600">
        Pronađeno <strong>{{ $sellers->total() }}</strong> prodavaca
    </div>
    
    {{-- Sellers Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($sellers as $seller)
            <a href="{{ route('sellers.show', $seller) }}" class="block group">
                <div class="card-hover">
                    {{-- Seller Avatar --}}
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden">
                            @if($seller->profile?->avatar_url)
                                <img src="{{ $seller->profile->avatar_url }}" alt="{{ $seller->name }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-2xl font-bold">
                                    {{ strtoupper(substr($seller->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $seller->name }}
                            </h3>
                            @if($seller->hasRole('verified_seller'))
                                <span class="inline-flex items-center text-xs text-blue-600 font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Verifikovani prodavac
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Reputacija</p>
                            <p class="font-semibold text-gray-900">
                                {{ number_format($seller->trust_score ?? 0, 1) }} ★
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Prodaja</p>
                            <p class="font-semibold text-gray-900">
                                {{ $seller->total_sales ?? 0 }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Aukcije</p>
                            <p class="font-semibold text-gray-900">
                                {{ $seller->active_auctions_count ?? 0 }}
                            </p>
                        </div>
                    </div>
                    
                    {{-- Categories --}}
                    @if($seller->categories && $seller->categories->count() > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($seller->categories->take(3) as $category)
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    
    {{-- Pagination --}}
    <div class="mt-8">
        {{ $sellers->links() }}
    </div>
</div>
