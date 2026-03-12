<div class="space-y-6">
    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Otprema</h2>
        @if ($statusMessage)
            <x-alert variant="success">{{ $statusMessage }}</x-alert>
        @endif
        @if ($markedShipped)
            <x-alert variant="success">Tracking podaci su sačuvani i status pošiljke je ažuriran.</x-alert>
        @endif
        <x-select wire:model.live="courier" name="courier" label="Kurirska služba" :options="['' => 'Odaberi kurira', 'euroexpress' => 'EuroExpress', 'postexpress' => 'PostExpress', 'bhposta' => 'BH Pošta', 'other' => 'Drugi kurir']" />
        <x-input wire:model.live="trackingNumber" name="tracking_number" label="Tracking broj" placeholder="EE123456789BA" />
        <div class="flex flex-col gap-3">
            <x-button wire:click="markShipped">Označi kao poslano</x-button>
            <x-button variant="ghost" :href="route('seller.orders.export')">Export u CSV</x-button>
        </div>
    </x-card>

    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Operativne napomene</h2>
        <x-alert variant="info">Tracking broj i kurir se odmah spremaju na shipment zapis, tako da buyer može pratiti status narudžbe.</x-alert>
    </x-card>
</div>
