{{--
    Admin Analytics UI
    T-1552: Dashboard sa Chart.js: GMV trend, user growth, auction conversion
--}}

<div class="space-y-6">
    {{-- Period Selector --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Analitika</h2>
        <div class="flex items-center space-x-2">
            <button
                wire:click="setPeriod('7d')"
                class="px-4 py-2 rounded-lg {{ $period === '7d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
            >
                7 dana
            </button>
            <button
                wire:click="setPeriod('30d')"
                class="px-4 py-2 rounded-lg {{ $period === '30d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
            >
                30 dana
            </button>
            <button
                wire:click="setPeriod('90d')"
                class="px-4 py-2 rounded-lg {{ $period === '90d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
            >
                90 dana
            </button>
        </div>
    </div>
    
    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total GMV --}}
        <div class="card">
            <p class="text-sm text-gray-600 mb-2">Ukupno GMV</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalGmv, 0) }} KM</p>
            <p class="text-sm {{ $gmvChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $gmvChange >= 0 ? '↑' : '↓' }} {{ abs($gmvChange) }}%
            </p>
        </div>
        
        {{-- Active Users --}}
        <div class="card">
            <p class="text-sm text-gray-600 mb-2">Aktivni korisnici</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($activeUsers) }}</p>
            <p class="text-sm {{ $userChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $userChange >= 0 ? '↑' : '↓' }} {{ abs($userChange) }}%
            </p>
        </div>
        
        {{-- Active Auctions --}}
        <div class="card">
            <p class="text-sm text-gray-600 mb-2">Aktivne aukcije</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($activeAuctions) }}</p>
            <p class="text-sm {{ $auctionChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $auctionChange >= 0 ? '↑' : '↓' }} {{ abs($auctionChange) }}%
            </p>
        </div>
        
        {{-- Conversion Rate --}}
        <div class="card">
            <p class="text-sm text-gray-600 mb-2">Konverzija</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($conversionRate, 1) }}%</p>
            <p class="text-sm {{ $conversionChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $conversionChange >= 0 ? '↑' : '↓' }} {{ abs($conversionChange) }}%
            </p>
        </div>
    </div>
    
    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- GMV Trend --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">GMV Trend</h3>
            <canvas id="gmvTrendChart" wire:ignore></canvas>
        </div>
        
        {{-- User Growth --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Rast korisnika</h3>
            <canvas id="userGrowthChart" wire:ignore></canvas>
        </div>
        
        {{-- Auction Conversion --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Konverzija aukcija</h3>
            <canvas id="conversionChart" wire:ignore></canvas>
        </div>
        
        {{-- Top Sellers --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Top prodavci</h3>
            <canvas id="topSellersChart" wire:ignore></canvas>
        </div>
    </div>
    
    {{-- Additional Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Nedavna aktivnost</h3>
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-3 text-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="flex-1">{{ $activity->description }}</span>
                        <span class="text-gray-500 text-xs">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Disputes Overview --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Sporovi</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Otvoreni</span>
                    <span class="font-semibold">{{ $openDisputes }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Riješeni</span>
                    <span class="font-semibold">{{ $resolvedDisputes }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Prosječno vrijeme</span>
                    <span class="font-semibold">{{ $avgResolutionTime }} dana</span>
                </div>
            </div>
        </div>
        
        {{-- Revenue Breakdown --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Prihodi</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Komisije</span>
                    <span class="font-semibold">{{ number_format($commissionRevenue, 0) }} KM</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subskripcije</span>
                    <span class="font-semibold">{{ number_format($subscriptionRevenue, 0) }} KM</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Featured</span>
                    <span class="font-semibold">{{ number_format($featuredRevenue, 0) }} KM</span>
                </div>
                <div class="flex justify-between pt-3 border-t">
                    <span class="font-semibold">Ukupno</span>
                    <span class="font-bold text-green-600">{{ number_format($totalRevenue, 0) }} KM</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Livewire.on('updateAnalytics', (data) => {
        // GMV Trend Chart
        new Chart(document.getElementById('gmvTrendChart'), {
            type: 'line',
            data: {
                labels: data.gmvLabels,
                datasets: [{
                    label: 'GMV (KM)',
                    data: data.gmvData,
                    borderColor: '#2563eb',
                    tension: 0.3
                }]
            }
        });
        
        // User Growth Chart
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'bar',
            data: {
                labels: data.userLabels,
                datasets: [{
                    label: 'Novi korisnici',
                    data: data.userData,
                    backgroundColor: '#10b981'
                }]
            }
        });
        
        // Conversion Chart
        new Chart(document.getElementById('conversionChart'), {
            type: 'line',
            data: {
                labels: data.conversionLabels,
                datasets: [{
                    label: 'Konverzija (%)',
                    data: data.conversionData,
                    borderColor: '#f59e0b',
                    tension: 0.3
                }]
            }
        });
        
        // Top Sellers Chart
        new Chart(document.getElementById('topSellersChart'), {
            type: 'bar',
            data: {
                labels: data.sellerLabels,
                datasets: [{
                    label: 'Prodaja (KM)',
                    data: data.sellerData,
                    backgroundColor: '#8b5cf6'
                }]
            },
            options: {
                indexAxis: 'y'
            }
        });
    });
</script>
@endpush
