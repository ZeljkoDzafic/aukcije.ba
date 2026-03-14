@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('admin_heading', 'Admin Dashboard')

@section('content')
<section class="space-y-8">
    <div class="panel-hero">
        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-4">
                <p class="panel-kicker">Marketplace control room</p>
                <div>
                    <h2 class="text-3xl font-semibold text-white sm:text-4xl">Operativni centar za moderaciju, rizik i sadržaj.</h2>
                    <p class="mt-3 max-w-2xl text-sm text-slate-200 sm:text-base">Najbitniji tokovi su izvučeni naprijed: sporovi, KYC redovi, aukcije pod pažnjom i sadržaj koji traži reakciju.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <x-button :href="route('admin.disputes.index')">Otvori sporove</x-button>
                    <x-button variant="ghost" :href="route('admin.kyc.index')" class="border-white/20 bg-white/10 text-white hover:bg-white/15">KYC pregled</x-button>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($operationsInbox->take(4) as $item)
                    <a href="{{ $item['href'] }}" class="panel-metric transition hover:bg-white/15">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-300">{{ $item['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $item['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-200">{{ $item['hint'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-card class="panel-shell-soft market-sheen">
                <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stat['value'] }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-3 md:grid-cols-3">
        @foreach ($operationsInbox as $item)
            <a href="{{ $item['href'] }}" class="panel-shell-soft transition hover:-translate-y-0.5 hover:border-slate-300">
                <p class="text-sm text-slate-500">{{ $item['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $item['hint'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-8 xl:grid-cols-[1fr_0.8fr]">
        <x-card class="panel-shell space-y-5">
            <h2 class="text-2xl font-semibold text-slate-900">Operativni prioriteti</h2>
            <div class="grid gap-3">
                @foreach ($priorities as $item)
                    <x-alert :variant="$item['variant']">{{ $item['text'] }}</x-alert>
                @endforeach
            </div>
        </x-card>

        <div class="space-y-8">
            <x-card class="panel-shell space-y-4">
                <h2 class="text-2xl font-semibold text-slate-900">Admin inbox</h2>
                <div class="space-y-3">
                    @foreach ($ctaQueue as $item)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 px-4 py-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $item['title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $item['description'] }}</p>
                                </div>
                                <x-button variant="ghost" :href="$item['href']">{{ $item['title'] }}</x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card class="panel-shell space-y-4">
                <h2 class="text-2xl font-semibold text-slate-900">Nedavna aktivnost</h2>
                <div class="space-y-3 text-sm text-slate-700">
                    @foreach ($activity as $entry)
                        <div class="panel-subtle-card">{{ $entry }}</div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</section>
@endsection
