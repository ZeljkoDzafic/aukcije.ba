@props(['count' => 4])

<div class="space-y-4">
    <div class="rounded-[1.5rem] bg-gradient-to-br from-slate-100 to-slate-200 p-16 text-center text-sm font-medium text-slate-500">Glavna slika</div>
    <div class="grid grid-cols-4 gap-3">
        @for ($i = 0; $i < $count; $i++)
            <div class="rounded-2xl bg-slate-100 p-5 text-center text-xs text-slate-500">Slika</div>
        @endfor
    </div>
</div>
