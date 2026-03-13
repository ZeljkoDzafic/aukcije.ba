@php
    $user = $user ?? null;
@endphp

<div class="space-y-6">
    @if ($statusMessage)
        <x-alert variant="success">{{ $statusMessage }}</x-alert>
    @endif

    <div class="grid gap-3 sm:grid-cols-2">
        <x-button variant="secondary" wire:click="moderate('toggle-seller-access')">Toggle seller pristup</x-button>
        <x-button variant="danger" wire:click="moderate('ban')">Ban / Unban korisnika</x-button>
        <x-button variant="ghost" wire:click="moderate('force-kyc-review')">Force KYC review</x-button>
        <x-button variant="ghost" wire:click="moderate('reset-password')">Reset lozinke</x-button>
    </div>

    <x-input wire:model.live="adminNote" name="admin_note" type="textarea" label="Admin decision note" />

    @if ($user)
        <x-data-table :headers="['Sekcija', 'Vrijednost']">
            <tr class="table-row"><td class="px-4 py-3">Aktivne aukcije</td><td class="px-4 py-3">{{ $user->auctions->count() }}</td></tr>
            <tr class="table-row"><td class="px-4 py-3">Role</td><td class="px-4 py-3">{{ method_exists($user, 'roleSummary') ? $user->roleSummary() : 'buyer' }}</td></tr>
            <tr class="table-row"><td class="px-4 py-3">Ukupni bidovi</td><td class="px-4 py-3">{{ $user->bids()->count() }}</td></tr>
            <tr class="table-row"><td class="px-4 py-3">Transakcije</td><td class="px-4 py-3">{{ $user->wallet?->transactions()->count() ?? 0 }}</td></tr>
            <tr class="table-row"><td class="px-4 py-3">Prosječna ocjena</td><td class="px-4 py-3">{{ number_format((float) $user->trust_score, 1) }}</td></tr>
        </x-data-table>
    @endif
</div>
