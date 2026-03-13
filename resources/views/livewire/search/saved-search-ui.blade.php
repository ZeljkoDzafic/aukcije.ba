{{--
    Saved Search UI Component
    T-1251: "Sačuvaj ovu pretragu" sa bell iconom
--}}

@props(['searchQuery', 'filters' => []])

<div x-data="savedSearch()" class="relative inline-block">
    {{-- Save Search Button --}}
    <button
        @click="toggleSave"
        class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ isSaved ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} hover:bg-blue-200 transition-colors"
    >
        <svg class="w-5 h-5" :class="isSaved ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        <span>{{ isSaved ? 'Sačuvano' : 'Sačuvaj pretragu' }}</span>
    </button>
    
    {{-- Saved Searches Dropdown --}}
    <div
        x-show="showDropdown"
        @click.away="showDropdown = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Moje sačuvane pretrage</h3>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @forelse($savedSearches as $search)
                <div class="p-4 hover:bg-gray-50 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $search->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $search->query }}</p>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $search->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <button
                            wire:click="deleteSearch({{ $search->id }})"
                            class="text-red-600 hover:text-red-700"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    
                    <label class="flex items-center mt-3 text-sm">
                        <input
                            type="checkbox"
                            wire:model="searches.{{ $loop->index }}.notifications"
                            wire:change="toggleNotifications({{ $search->id }})"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-gray-700">Obavijesti</span>
                    </label>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <p>Nema sačuvanih pretraga</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- Notification Bell --}}
    @if($savedSearches->count() > 0)
        <div class="absolute -top-1 -right-1">
            <span class="flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
        </div>
    @endif
</div>

@push('scripts')
<script>
function savedSearch() {
    return {
        isSaved: @json($isSaved),
        showDropdown: false,
        
        toggleSave() {
            if (this.isSaved) {
                @this.removeSearch()
            } else {
                @this.saveSearch(@json($searchQuery), @json($filters))
            }
            this.isSaved = !this.isSaved
        }
    }
}
</script>
@endpush
