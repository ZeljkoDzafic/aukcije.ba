@props(['headers' => []])

<div class="table-container">
    <table class="table">
        <thead class="table-header">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
