<div class="space-y-6">
    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Otprema</h2>
        @if ($statusMessage)
            <x-alert variant="success">{{ $statusMessage }}</x-alert>
        @endif
        @if ($markedShipped)
            <x-alert variant="success">Narudžba je označena kao poslana. Backend tracking sync tek treba biti priključen.</x-alert>
        @endif
        <x-input wire:model.live="trackingNumber" name="tracking_number" label="Tracking broj" placeholder="EE123456789BA" />
        <x-input wire:model.live="courier" name="courier" label="Kurirska služba" placeholder="EuroExpress" />
        <div class="flex flex-col gap-3">
            <x-button wire:click="markShipped">Označi kao poslano</x-button>
            <x-button variant="ghost" :href="route('seller.orders.export')">Export u CSV</x-button>
        </div>
    </x-card>

    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Operativne napomene</h2>
        <x-alert variant="info">Automatski tovarni list i tracking webhook mogu se povezati kada shipping integracija bude završena.</x-alert>
    </x-card>
</div>
