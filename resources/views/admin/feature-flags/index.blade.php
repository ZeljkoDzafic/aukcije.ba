@extends('layouts.admin')

@section('title', 'Feature Flags')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Feature Flags</h1>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Create new flag form --}}
    <div class="card mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Nova funkcionalnost</h2>
        </div>
        <div class="px-6 py-5">
            <form method="POST" action="{{ route('admin.feature-flags.store') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Naziv <span class="text-gray-400 font-normal">(samo mala slova i podcrta, npr. <code class="text-xs">dark_mode</code>)</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="naziv_funkcionalnosti"
                        pattern="^[a-z_]+$"
                        maxlength="100"
                        required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Opis (opcionalno)</label>
                    <input
                        type="text"
                        id="description"
                        name="description"
                        value="{{ old('description') }}"
                        placeholder="Kratki opis svrhe zastavice"
                        maxlength="255"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 @error('description') border-red-500 @enderror"
                    >
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    >
                    <label for="is_active" class="text-sm font-medium text-gray-700 whitespace-nowrap">Aktivna odmah</label>
                </div>
                <div>
                    <button type="submit" class="btn-primary whitespace-nowrap">
                        Dodaj zastavicu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Flags table --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Sve zastavice
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $flags->count() }} ukupno)</span>
            </h2>
        </div>

        @if ($flags->isEmpty())
            <div class="px-6 py-10 text-center text-gray-500 text-sm">
                Nema definiranih feature flagova. Dodajte prvu zastavicu gore.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Naziv</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Opis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kreiran</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($flags as $flag)
                            <tr class="table-row hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <code class="text-sm font-mono font-semibold text-gray-900">{{ $flag->name }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $flag->description ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($flag->is_active)
                                        <span class="badge-success inline-flex items-center gap-1 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Aktivna
                                        </span>
                                    @else
                                        <span class="badge-danger inline-flex items-center gap-1 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Neaktivna
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $flag->created_at->format('d.m.Y.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        {{-- Toggle --}}
                                        <form method="POST" action="{{ route('admin.feature-flags.toggle', $flag) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="text-sm font-medium {{ $flag->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }} transition-colors"
                                                title="{{ $flag->is_active ? 'Deaktiviraj' : 'Aktiviraj' }}"
                                            >
                                                {{ $flag->is_active ? 'Deaktiviraj' : 'Aktiviraj' }}
                                            </button>
                                        </form>

                                        <span class="text-gray-300">|</span>

                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.feature-flags.destroy', $flag) }}"
                                            onsubmit="return confirm('Jeste li sigurni da želite obrisati zastavicu \'{{ addslashes($flag->name) }}\'?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors"
                                                title="Obriši"
                                            >
                                                Obriši
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
