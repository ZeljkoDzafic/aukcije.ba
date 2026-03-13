<div class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-4">
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Korisnici</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['users'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Aukcije</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['auctions'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Prihod</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['revenue'] }}</p></x-card>
        <x-card class="space-y-2"><p class="text-sm text-slate-500">Povjerenje</p><p class="text-3xl font-semibold text-slate-900">{{ $cards['trust'] }}</p></x-card>
    </div>

    <div class="flex flex-wrap gap-3 text-sm">
        @foreach (['users' => 'Korisnici', 'auctions' => 'Aukcije', 'revenue' => 'Prihod', 'trust' => 'Povjerenje'] as $key => $label)
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
                    @foreach (($chartSeries[$tab] ?? []) as $index => $point)
                        <div class="flex h-full flex-col justify-end gap-2">
                            <div class="rounded-t-2xl bg-trust-600/85" style="height: {{ max(12, min(100, (int) round($point / max(1, collect($chartSeries[$tab] ?? [1])->max()) * 100))) }}%"></div>
                            <span class="text-center text-[11px] text-slate-500">{{ $chartLabels[$tab][$index] ?? ('P'.($index + 1)) }}</span>
                            <span class="text-center text-xs font-medium text-slate-700">{{ $point }}</span>
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

    <div class="grid gap-6 xl:grid-cols-2">
        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">GMV trend</h2>
                <span class="text-sm text-slate-500">{{ $range }} dana</span>
            </div>
            <canvas id="admin-gmv-chart" height="220"></canvas>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Rast korisnika</h2>
                <span class="text-sm text-slate-500">{{ $range }} dana</span>
            </div>
            <canvas id="admin-users-chart" height="220"></canvas>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Aktivnost aukcija</h2>
                <span class="text-sm text-slate-500">{{ $range }} dana</span>
            </div>
            <canvas id="admin-auctions-chart" height="220"></canvas>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Top prodavci</h2>
                <span class="text-sm text-slate-500">Po broju narudžbi</span>
            </div>
            <canvas id="admin-sellers-chart" height="220"></canvas>
        </x-card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let adminGmvChart;
    let adminUsersChart;
    let adminAuctionsChart;
    let adminSellersChart;

    const destroyAdminCharts = () => {
        [adminGmvChart, adminUsersChart, adminAuctionsChart, adminSellersChart].forEach((chart) => {
            if (chart) {
                chart.destroy();
            }
        });
    };

    const renderAdminCharts = (payload) => {
        if (typeof Chart === 'undefined') {
            return;
        }

        destroyAdminCharts();

        adminGmvChart = new Chart(document.getElementById('admin-gmv-chart'), {
            type: 'line',
            data: {
                labels: payload.labels.revenue,
                datasets: [{
                    label: 'GMV',
                    data: payload.series.revenue,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    fill: true,
                    tension: 0.35,
                }],
            },
        });

        adminUsersChart = new Chart(document.getElementById('admin-users-chart'), {
            type: 'bar',
            data: {
                labels: payload.labels.users,
                datasets: [{
                    label: 'Novi korisnici',
                    data: payload.series.users,
                    backgroundColor: '#0f766e',
                    borderRadius: 12,
                }],
            },
        });

        adminAuctionsChart = new Chart(document.getElementById('admin-auctions-chart'), {
            type: 'line',
            data: {
                labels: payload.labels.auctions,
                datasets: [{
                    label: 'Aktivne aukcije',
                    data: payload.series.auctions,
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.15)',
                    fill: true,
                    tension: 0.3,
                }],
            },
        });

        adminSellersChart = new Chart(document.getElementById('admin-sellers-chart'), {
            type: 'bar',
            data: {
                labels: payload.topSellerLabels,
                datasets: [{
                    label: 'Narudžbe',
                    data: payload.topSellerValues,
                    backgroundColor: '#7c3aed',
                    borderRadius: 12,
                }],
            },
            options: {
                indexAxis: 'y',
            },
        });
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('admin-analytics-update', (event) => {
            renderAdminCharts(event.payload);
        });
    });
</script>
@endpush
