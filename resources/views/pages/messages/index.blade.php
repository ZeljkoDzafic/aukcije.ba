@extends('layouts.app')

@section('title', 'Poruke')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Inbox</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Poruke</h1>
                <p class="mt-2 text-slate-600">Kupac i seller komunikacija žive na istom računu, po threadovima vezanim za aukcije.</p>
            </div>
            <a href="{{ route('auctions.index') }}" class="link">Pregledaj aukcije</a>
        </div>

        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Konverzacije</h2>
                    <span class="text-sm text-slate-500">{{ $threads->count() }} aktivnih</span>
                </div>

                @if ($threads->isEmpty())
                    <x-alert variant="info">Još nema poruka. Kontakt sa drugim korisnicima pojavit će se ovdje čim krene komunikacija oko aukcije.</x-alert>
                @else
                    <div class="space-y-3">
                        @foreach ($threads as $thread)
                            <a href="{{ route('messages.index', ['thread' => $thread['thread_key']]) }}" class="block rounded-3xl border px-4 py-4 transition {{ ($selectedThread['thread_key'] ?? null) === $thread['thread_key'] ? 'border-trust-400 bg-trust-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-semibold text-slate-900">{{ $thread['contact'] }}</h3>
                                            @if ($thread['unread_count'] > 0)
                                                <x-badge variant="warning">{{ $thread['unread_count'] }}</x-badge>
                                            @endif
                                            @if ($thread['action_required'] ?? false)
                                                <x-badge variant="trust">Čeka tvoj odgovor</x-badge>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-500">{{ $thread['auction_title'] }}</p>
                                        <p class="line-clamp-2 text-sm text-slate-700">{{ $thread['last_message'] }}</p>
                                    </div>
                                    <div class="text-right text-xs text-slate-500">
                                        <p>{{ $thread['last_message_at'] }}</p>
                                        <p class="mt-1">{{ $thread['message_count'] }} poruka</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card class="space-y-5">
                @if (! $selectedThread)
                    <x-alert variant="info">Odaberi thread lijevo da vidiš poruke i pošalješ odgovor.</x-alert>
                @else
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">{{ $selectedThread['contact'] }}</h2>
                            <p class="text-sm text-slate-500">{{ $selectedThread['auction_title'] }}</p>
                        </div>
                        @if ($selectedThread['auction_id'])
                            <a href="{{ route('auctions.show', ['auction' => $selectedThread['auction_id']]) }}" class="link">Otvori aukciju</a>
                        @endif
                    </div>

                    @if (! empty($systemThreadNote))
                        <div class="rounded-2xl border border-trust-200 bg-trust-50 px-4 py-3 text-sm text-trust-900">
                            {{ $systemThreadNote }}
                        </div>
                    @endif

                    <div class="space-y-3">
                        @foreach ($threadMessages as $message)
                            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xl rounded-3xl px-4 py-3 text-sm {{ ($message->message_type ?? 'user') === 'system' ? 'border border-trust-200 bg-trust-50 text-trust-900' : ($message->sender_id === auth()->id() ? 'bg-trust-700 text-white' : 'bg-slate-100 text-slate-800') }}">
                                    @if (($message->message_type ?? 'user') === 'system')
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-trust-700">Sistemska poruka</p>
                                    @endif
                                    <p>{{ $message->content }}</p>
                                    @if ($message->attachment_url)
                                        <a href="{{ $message->attachment_url }}" target="_blank" rel="noreferrer" class="mt-3 inline-flex rounded-full border px-3 py-1 text-xs font-medium {{ $message->sender_id === auth()->id() && ($message->message_type ?? 'user') !== 'system' ? 'border-trust-100 text-white' : 'border-slate-300 text-slate-700' }}">
                                            {{ $message->attachment_name ?: 'Otvori prilog' }}
                                        </a>
                                    @endif
                                    <p class="mt-2 text-xs {{ $message->sender_id === auth()->id() ? 'text-trust-100' : 'text-slate-500' }}">
                                        {{ $message->created_at?->format('d.m.Y. H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data" class="space-y-4 border-t border-slate-100 pt-4">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedThread['other_user_id'] }}">
                        <input type="hidden" name="auction_id" value="{{ $selectedThread['auction_id'] }}">
                        <x-input name="content" type="textarea" label="Odgovor" placeholder="Napiši poruku kupcu ili selleru..." />
                        <div class="grid gap-4 md:grid-cols-2">
                            <x-input name="attachment_name" label="Naziv priloga" placeholder="npr. detaljna-fotografija.jpg" />
                            <x-input name="attachment_url" type="url" label="Link priloga" placeholder="https://..." />
                        </div>
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4">
                            <label class="block text-sm font-medium text-slate-700" for="attachment_file">Otpremi prilog</label>
                            <input id="attachment_file" name="attachment_file" type="file" class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-trust-700 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white">
                            <p class="mt-2 text-xs text-slate-500">Podržano: JPG, PNG, WEBP, PDF do 5 MB.</p>
                        </div>
                        <x-button type="submit">Pošalji odgovor</x-button>
                    </form>
                @endif
            </x-card>
        </div>
    </div>
</section>
@endsection
