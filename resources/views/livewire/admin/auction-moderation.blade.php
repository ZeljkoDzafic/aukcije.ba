<div class="space-y-6">
    <x-card class="space-y-4">
        <div class="flex flex-wrap gap-3 text-sm">
            @foreach (['all' => 'Sve', 'reported' => 'Reported', 'pending review' => 'Na čekanju', 'active' => 'Aktivne', 'cancelled' => 'Otkazane'] as $key => $label)
                <button type="button" wire:click="$set('filter', '{{ $key }}')" class="rounded-full px-4 py-2 {{ $filter === $key ? 'bg-trust-50 text-trust-700' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-3">
            <x-button variant="ghost" wire:click="selectVisible">Označi vidljive</x-button>
            <x-button variant="ghost" wire:click="clearSelection">Poništi izbor</x-button>
            <x-button variant="secondary" wire:click="applyBulk('approve-pending')">Approve all pending</x-button>
            <x-button variant="secondary" wire:click="applyBulk('feature-selected')">Feature selected</x-button>
            <x-button variant="ghost" wire:click="applyBulk('cancel-expired')">Cancel expired</x-button>
        </div>

        <x-input wire:model.live="bulkNote" name="bulk_note" type="textarea" label="Bulk decision note" />

        @if ($selectedAuctionIds !== [])
            <x-alert variant="info">{{ count($selectedAuctionIds) }} aukcija je označeno za bulk akciju.</x-alert>
        @endif

        @if ($feedback)
            <x-alert variant="info">{{ $feedback }}</x-alert>
        @endif

        <x-data-table :headers="['Izbor', 'Aukcija', 'Status', 'Seller', 'Napomena', 'Akcije']">
            @foreach ($this->visibleAuctions as $auction)
                <tr class="table-row">
                    <td class="px-4 py-3">
                        <input
                            type="checkbox"
                            wire:click="toggleSelection('{{ $auction['id'] }}')"
                            @checked(in_array($auction['id'], $selectedAuctionIds, true))
                            class="h-4 w-4 rounded border-slate-300 text-trust-600 focus:ring-trust-500"
                        />
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $auction['title'] }}</td>
                    <td class="px-4 py-3">{{ $auction['status'] }}</td>
                    <td class="px-4 py-3">{{ $auction['seller'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $auction['latest_note'] ?? 'Nema bilješke' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <x-button variant="ghost" :href="route('admin.auctions.show', ['auction' => $auction['id']])">Detalj</x-button>
                            <x-button variant="secondary" wire:click="applyAction({{ $auction['id'] }}, 'approve')">Approve</x-button>
                            <x-button variant="secondary" wire:click="applyAction({{ $auction['id'] }}, 'feature')">Feature</x-button>
                            <x-button variant="danger" wire:click="applyAction({{ $auction['id'] }}, 'cancel')">Cancel</x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </x-card>
</div>
