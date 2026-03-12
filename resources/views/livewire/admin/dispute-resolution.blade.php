<div class="space-y-6">
    @if ($feedback)
        <x-alert variant="info">{{ $feedback }}</x-alert>
    @endif

    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Akcije moderatora</h2>
        <div class="grid gap-3">
            <x-button variant="secondary" wire:click="resolve('request-more-evidence')">Traži dodatne dokaze</x-button>
            <x-button wire:click="resolve('resolve-for-buyer')">Riješi u korist kupca</x-button>
            <x-button variant="ghost" wire:click="resolve('partial-refund')">Partial refund</x-button>
            <x-button variant="danger" wire:click="resolve('resolve-for-seller')">Riješi u korist sellera</x-button>
        </div>
    </x-card>

    <x-card class="space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Komunikacijski log</h2>
        <div class="space-y-3">
            @foreach ($messages as $entry)
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">{{ $entry['author'] }}:</span>
                    {{ $entry['body'] }}
                </div>
            @endforeach
        </div>
        <x-input wire:model.live="message" name="admin_dispute_message" type="textarea" label="Nova admin poruka" />
        <x-button wire:click="addMessage">Dodaj poruku</x-button>
    </x-card>
</div>
