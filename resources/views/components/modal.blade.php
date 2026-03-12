@props(['title' => 'Dialog'])

<div {{ $attributes->merge(['class' => 'modal-backdrop']) }}>
    <div class="modal-content">
        <div class="modal-panel p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">{{ $title }}</h2>
                <button type="button" class="rounded-full p-2 text-slate-500 hover:bg-slate-100">×</button>
            </div>
            <div class="mt-4">{{ $slot }}</div>
        </div>
    </div>
</div>
