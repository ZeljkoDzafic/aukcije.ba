<div class="space-y-6">
    @if ($statusMessage !== '')
        <x-alert variant="success">{{ $statusMessage }}</x-alert>
    @endif

    <x-card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
            <x-input wire:model.live="name" name="template_name" label="Naziv template-a" placeholder="Satovi premium format" />
            <label class="block text-sm font-medium text-slate-700">
                Osnovna aukcija
                <select wire:model.live="sourceAuctionId" class="input mt-2">
                    <option value="">Posljednja aukcija</option>
                    @foreach ($auctionOptions as $auction)
                        <option value="{{ $auction->id }}">{{ $auction->title }}</option>
                    @endforeach
                </select>
            </label>
            <div class="pt-8">
                <x-button wire:click="saveTemplate">Sačuvaj template</x-button>
            </div>
        </div>
    </x-card>

    <x-card class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Moji template-i</h2>
            <x-badge variant="trust">{{ $templates->count() }} kom</x-badge>
        </div>

        <div class="space-y-3">
            @forelse ($templates as $template)
                <div class="rounded-2xl border border-slate-200 px-4 py-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-medium text-slate-900">{{ $template->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $template->created_at?->diffForHumans() }}</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <x-button variant="ghost" wire:click="createFromTemplate('{{ $template->id }}')">Kreiraj draft</x-button>
                            <x-button variant="danger" wire:click="deleteTemplate('{{ $template->id }}')">Ukloni</x-button>
                        </div>
                    </div>
                </div>
            @empty
                <x-alert variant="info">Još nema template-a. Sačuvaj jednu od postojećih aukcija kao osnovu za buduće objave.</x-alert>
            @endforelse
        </div>
    </x-card>
</div>
