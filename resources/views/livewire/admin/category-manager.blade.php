<div class="grid gap-6 xl:grid-cols-[1fr_0.85fr]">
    <x-card class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Tree view</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Hijerarhija kategorija</h1>
            </div>
            <x-button>Nova kategorija</x-button>
        </div>

        <div class="space-y-3">
            @foreach ($categories as $category)
                <button type="button" wire:click="selectCategory('{{ $category['name'] }}')" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-left">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-slate-900">{{ $category['name'] }}</p>
                            <p class="text-sm text-slate-600">{{ $category['count'] }} aukcija · {{ strtolower($category['status']) }}</p>
                        </div>
                        <div class="flex gap-3">
                            <x-button variant="ghost" wire:click.stop="moveCategory('{{ $category['name'] }}', 'up')">↑</x-button>
                            <x-button variant="ghost" wire:click.stop="moveCategory('{{ $category['name'] }}', 'down')">↓</x-button>
                            <x-badge variant="trust">{{ $category['status'] }}</x-badge>
                            <span class="text-sm font-medium text-trust-700">Uredi</span>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2 pl-4 text-sm text-slate-600">
                        @foreach ($category['children'] as $child)
                            <div class="rounded-xl bg-white px-3 py-2">{{ $child }}</div>
                        @endforeach
                    </div>
                </button>
            @endforeach
        </div>
    </x-card>

    <div class="space-y-6">
        <x-card class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Create / edit</h2>
            @if ($statusMessage)
                <x-alert variant="success">{{ $statusMessage }}</x-alert>
            @endif
            <div class="grid gap-4">
                <x-input wire:model.live="name" name="category_name" label="Naziv" />
                <x-input wire:model.live="slug" name="category_slug" label="Slug" />
                <x-input wire:model.live="icon" name="category_icon" label="Ikonica" />
                <x-select wire:model.live="parent" name="category_parent" label="Parent" :options="['' => 'Root kategorija', 'electronics' => 'Elektronika', 'watches' => 'Satovi']" />
            </div>
            <div class="flex flex-wrap gap-3">
                <x-button wire:click="saveCategory">Sačuvaj</x-button>
                <x-button variant="secondary" wire:click="toggleSelectedCategory">Aktiviraj / deaktiviraj</x-button>
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Admin napomena</h2>
            <x-alert variant="info">Drag & drop reorder je pripremljen kroz tree layout, a ponašanje će se vezati na backend kada kategorije dobiju prave admin akcije.</x-alert>
        </x-card>
    </div>
</div>
