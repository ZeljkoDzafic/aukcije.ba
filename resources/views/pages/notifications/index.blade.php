@extends('layouts.app')

@section('title', 'Obavijesti')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Command center</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Obavijesti</h1>
                <p class="mt-2 text-slate-600">Outbid, poruke, isporuke i sistemske promjene na jednom mjestu.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('notifications.read-filtered') }}">
                    @csrf
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <x-button variant="ghost">Označi filter kao pročitano</x-button>
                </form>
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-button variant="ghost">Označi sve kao pročitano</x-button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        <x-card class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Pregled obavijesti</h2>
                <x-badge variant="trust">{{ $unreadCount }} nepročitanih</x-badge>
            </div>

            <div class="grid gap-3 md:grid-cols-3">
                @foreach ($summaryCards as $card)
                    <a href="{{ $card['href'] }}" class="rounded-2xl bg-slate-50 px-4 py-4 transition hover:bg-slate-100">
                        <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3">
                @foreach ($filters as $item)
                    <a
                        href="{{ route('notifications.index', ['filter' => $item['value'] === 'all' ? null : $item['value'], 'sort' => $sort !== 'priority' ? $sort : null]) }}"
                        class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-medium transition {{ $filter === $item['value'] ? 'border-trust-500 bg-trust-50 text-trust-800' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}"
                    >
                        <span>{{ $item['label'] }}</span>
                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $unreadByType[$item['value']] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3">
                @foreach ($sortOptions as $option)
                    <a
                        href="{{ route('notifications.index', ['filter' => $filter !== 'all' ? $filter : null, 'sort' => $option['value'] !== 'priority' ? $option['value'] : null]) }}"
                        class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-medium transition {{ $sort === $option['value'] ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}"
                    >
                        {{ $option['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-3">
                @forelse ($notifications as $notification)
                    <div class="rounded-3xl border px-5 py-4 {{ $notification->read_at ? 'border-slate-200 bg-white' : 'border-trust-200 bg-trust-50/70' }}">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-semibold text-slate-900">{{ $notification->title ?: 'Obavijest platforme' }}</h3>
                                    <x-badge :variant="$notification->read_at ? 'info' : 'warning'">
                                        {{ $notification->read_at ? 'Pročitano' : 'Novo' }}
                                    </x-badge>
                                    @if (in_array($notification->type, ['outbid', 'auction_won', 'shipment', 'item_shipped'], true))
                                        <x-badge variant="danger">Hitno</x-badge>
                                    @elseif ($notification->type === 'saved_search_match')
                                        <x-badge variant="trust">Signal</x-badge>
                                    @endif
                                    @if ($notification->priority_label)
                                        <x-badge variant="info">{{ $notification->priority_label }}</x-badge>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-700">{{ $notification->body ?: 'Nova promjena je evidentirana na vašem računu.' }}</p>
                                <p class="text-xs text-slate-500">{{ $notification->created_at?->diffForHumans() }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                @if ($notification->action_href && $notification->action_label)
                                    <a href="{{ $notification->action_href }}" class="link">{{ $notification->action_label }}</a>
                                @endif

                                @if (! $notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', ['notification' => $notification->id]) }}">
                                        @csrf
                                        <x-button variant="ghost">Označi pročitano</x-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <x-alert variant="info">Još nema obavijesti. Kada dobijete poruku, shipping update ili promjenu statusa aukcije, pojavit će se ovdje.</x-alert>
                @endforelse
            </div>
        </x-card>
    </div>
</section>
@endsection
