<div class="space-y-6">
    @if ($statusMessage !== '')
        <x-alert variant="success">{{ $statusMessage }}</x-alert>
    @endif

    <x-card class="space-y-4">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="ghost" wire:click="selectAll">Označi prikazane</x-button>
            <x-button variant="ghost" wire:click="clearSelection">Poništi izbor</x-button>
            <x-button wire:click="apply('publish')">Objavi draftove</x-button>
            <x-button variant="ghost" wire:click="apply('end')">Završi aktivne</x-button>
            <x-button variant="ghost" wire:click="apply('clone')">Kloniraj u draft</x-button>
        </div>
        <p class="text-sm text-slate-500">Označeno: {{ count($selectedAuctionIds) }}</p>
    </x-card>

    <x-card class="space-y-4">
        <div class="space-y-3">
            @forelse ($auctions as $auction)
                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-4">
                    <input type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-trust-600" @checked(in_array($auction->id, $selectedAuctionIds, true)) wire:click="toggleSelection('{{ $auction->id }}')">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-slate-900">{{ $auction->title }}</p>
                            <x-badge :variant="$auction->status === 'active' ? 'success' : ($auction->status === 'draft' ? 'warning' : 'info')">{{ str_replace('_', ' ', $auction->status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Početna cijena: {{ number_format((float) $auction->start_price, 2, ',', '.') }} BAM</p>
                    </div>
                </label>
            @empty
                <x-alert variant="info">Nema aukcija za bulk rad.</x-alert>
            @endforelse
        </div>
    </x-card>
</div>
