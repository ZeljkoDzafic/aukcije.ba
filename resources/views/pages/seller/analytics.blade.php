@extends('layouts.seller')

@section('title', 'Analitika prodaje')
@section('seller_heading', 'Analitika')

@section('content')
<section class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- GMV --}}
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">GMV ({{ $period }})</p>
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.312-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.312.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($gmv, 0) }} KM</p>
            <p class="text-sm {{ $gmvChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $gmvChange >= 0 ? '↑' : '↓' }} {{ abs($gmvChange) }}%
            </p>
        </div>
        
        {{-- Sell-Through Rate --}}
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Sell-Through</p>
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($sellThroughRate, 1) }}%</p>
            <p class="text-sm {{ $sellThroughChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $sellThroughChange >= 0 ? '↑' : '↓' }} {{ abs($sellThroughChange) }}%
            </p>
        </div>
        
        {{-- Avg Days to Sell --}}
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Avg. dana za prodaju</p>
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $avgDaysToSell }} dana</p>
            <p class="text-sm {{ $avgDaysChange <= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $avgDaysChange <= 0 ? '↓' : '↑' }} {{ abs($avgDaysChange) }} dana
            </p>
        </div>
        
        {{-- Dispute Rate --}}
        <div class="card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Stopa sporova</p>
                <svg class="w-5 h-5 {{ $disputeRate < 5 ? 'text-green-500' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($disputeRate, 1) }}%</p>
            <p class="text-sm {{ $disputeChange <= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $disputeChange <= 0 ? '↓' : '↑' }} {{ abs($disputeChange) }}%
            </p>
        </div>
    </div>
    
    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- GMV Trend Chart --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">GMV Trend</h3>
            <canvas id="gmvChart" wire:ignore></canvas>
        </div>
        
        {{-- Top Categories --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-4">Top Kategorije</h3>
            <canvas id="categoriesChart" wire:ignore></canvas>
        </div>
    </div>
    
    {{-- Top Items --}}
    <div class="card">
        <h3 class="font-semibold text-gray-900 mb-4">Najprodavaniji artikli</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Artikal</th>
                        <th>Kategorija</th>
                        <th>Prodano</th>
                        <th>Ukupno</th>
                        <th>Prosječna cijena</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topItems as $item)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    @if($item->primaryImage)
                                        <img src="{{ $item->primaryImage->url }}" alt="" class="w-10 h-10 object-cover rounded" />
                                    @endif
                                    <span class="font-medium">{{ Str::limit($item->title, 40) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $item->category->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $item->sold_count }}</td>
                            <td>{{ number_format($item->total_revenue, 0) }} KM</td>
                            <td>{{ number_format($item->avg_price, 2) }} KM</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Period Selector --}}
    <div class="flex justify-center space-x-2">
        <a
            href="{{ route('seller.analytics', ['period' => '7d']) }}"
            class="px-4 py-2 rounded-lg {{ $period === '7d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
        >
            7 dana
        </a>
        <a
            href="{{ route('seller.analytics', ['period' => '30d']) }}"
            class="px-4 py-2 rounded-lg {{ $period === '30d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
        >
            30 dana
        </a>
        <a
            href="{{ route('seller.analytics', ['period' => '90d']) }}"
            class="px-4 py-2 rounded-lg {{ $period === '90d' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
        >
            90 dana
        </a>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gmvCtx = document.getElementById('gmvChart');
        if (gmvCtx) {
            new Chart(gmvCtx, {
                type: 'line',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7'],
                    datasets: [{
                        label: 'GMV (KM)',
                        data: [12, 19, 15, 26, 24, 32, 28],
                        borderColor: '#2563eb',
                        tension: 0.3
                    }]
                }
            });
        }

        const catCtx = document.getElementById('categoriesChart');
        if (catCtx) {
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Satovi', 'Elektronika', 'Kolekcionarstvo', 'Foto oprema', 'Ostalo'],
                    datasets: [{
                        data: [32, 26, 18, 14, 10],
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                    }]
                }
            });
        }
    });
</script>
@endpush
@endsection
