<div class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-4">
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Users</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['users'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Auctions</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['auctions'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Revenue</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['revenue'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Trust</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['trust'] }}</p></x-card>
    </div>

    <div class="flex flex-wrap gap-3 text-sm">
        @foreach (['users' => 'Users', 'auctions' => 'Auctions', 'revenue' => 'Revenue', 'trust' => 'Trust'] as $key => $label)
            <button type="button" wire:click="$set('tab', '{{ $key }}')" class="rounded-full px-4 py-2 {{ $tab === $key ? 'bg-trust-50 text-trust-700' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</button>
        @endforeach
        <select wire:model.live="range" class="rounded-full bg-slate-100 px-4 py-2 text-slate-600">
            <option value="7">7 dana</option>
            <option value="30">30 dana</option>
            <option value="90">90 dana</option>
        </select>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
        <x-card class="space-y-4">
            <p class="text-sm text-slate-500">Aktivni tab: {{ ucfirst($tab) }}</p>
            <div class="rounded-3xl bg-slate-100 p-6">
                <div class="grid h-64 grid-cols-6 items-end gap-3">
                    @foreach (($chartSeries[$tab] ?? []) as $point)
                        <div class="flex h-full flex-col justify-end gap-2">
                            <div class="rounded-t-2xl bg-trust-600/85" style="height: {{ max(12, min(100, (int) round($point / max(1, collect($chartSeries[$tab] ?? [1])->max()) * 100))) }}%"></div>
                            <span class="text-center text-xs text-slate-500">{{ $point }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Sažetak</h2>
            <x-alert variant="info">{{ $tabSummaries[$tab] ?? '' }}</x-alert>
            <x-data-table :headers="['Tab', 'Ključna metrika']">
                @foreach ($tabSummaries as $key => $summary)
                    <tr class="table-row">
                        <td class="px-4 py-3">{{ ucfirst($key) }}</td>
                        <td class="px-4 py-3">{{ $summary }}</td>
                    </tr>
                @endforeach
            </x-data-table>
            <x-button variant="ghost" :href="url('/api/v1/admin/statistics?format=csv')">Export CSV</x-button>
        </x-card>
    </div>
</div>
