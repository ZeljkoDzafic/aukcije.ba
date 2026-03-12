<div class="space-y-6">
    <x-card class="space-y-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Feature flags</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Upravljanje zastavicama</h1>
        </div>

        <div class="grid gap-4 lg:grid-cols-[1fr_1.3fr_auto]">
            <x-input wire:model.live="name" name="flag_name" label="Naziv" placeholder="proxy_bidding" />
            <x-input wire:model.live="description" name="flag_description" label="Opis" placeholder="Kratki opis flag-a" />
            <label class="flex items-center gap-3 pt-8 text-sm text-slate-700">
                <input wire:model.live="isActive" type="checkbox" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                <span>Aktivna odmah</span>
            </label>
        </div>

        <div class="flex flex-wrap gap-3">
            <x-button wire:click="save">Sačuvaj flag</x-button>
            <x-alert variant="info">`@@feature('flag_name')` i middleware `feature:flag_name` su dostupni za conditional rendering i route zaštitu.</x-alert>
        </div>

        @if ($feedback)
            <x-alert variant="success">{{ $feedback }}</x-alert>
        @endif
    </x-card>

    @foreach ($this->groupedFlags as $group => $flags)
        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">{{ $group }}</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $flags->count() }} zastavica</h2>
                </div>
                <x-badge variant="trust">{{ $group }}</x-badge>
            </div>

            <x-data-table :headers="['Naziv', 'Opis', 'Status', 'Akcija']">
                @foreach ($flags as $flag)
                    <tr class="table-row">
                        <td class="px-4 py-3 font-mono text-sm text-slate-900">{{ $flag['name'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $flag['description'] ?: 'Bez opisa' }}</td>
                        <td class="px-4 py-3">
                            <x-badge :variant="$flag['is_active'] ? 'success' : 'warning'">
                                {{ $flag['is_active'] ? 'Aktivna' : 'Neaktivna' }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-button variant="secondary" wire:click="toggle('{{ $flag['id'] }}')">
                                {{ $flag['is_active'] ? 'Isključi' : 'Uključi' }}
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        </x-card>
    @endforeach
</div>
