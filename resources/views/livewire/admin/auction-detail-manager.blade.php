@php
    $auction = $auction ?? null;
    $bidRows = $auction?->bids?->sortByDesc('amount')->take(10) ?? collect();
@endphp

<div class="space-y-6">
    @if ($statusMessage)
        <x-alert variant="success">{{ $statusMessage }}</x-alert>
    @endif

    <div class="flex gap-3">
        <x-button variant="secondary" wire:click="moderate('approve')">Approve</x-button>
        <x-button variant="ghost" wire:click="moderate('feature')">Feature</x-button>
        <x-button variant="danger" wire:click="moderate('cancel')">Cancel</x-button>
    </div>

    <x-input wire:model.live="moderationNote" name="moderation_note" type="textarea" label="Napomena moderatora" />

    @if ($auction)
        <x-data-table :headers="['Bidder', 'Iznos', 'Vrijeme']">
            @forelse ($bidRows as $bid)
                <tr class="table-row">
                    <td class="px-4 py-3">{{ $bid->user?->name ?? 'Anonimno' }}</td>
                    <td class="px-4 py-3">{{ number_format((float) $bid->amount, 2, ',', '.') }} BAM</td>
                    <td class="px-4 py-3">{{ optional($bid->created_at)->diffForHumans() }}</td>
                </tr>
            @empty
                <tr class="table-row">
                    <td class="px-4 py-3" colspan="3">Nema bidova za prikaz.</td>
                </tr>
            @endforelse
        </x-data-table>
    @endif
</div>
