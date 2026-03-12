<div class="space-y-8">
    <div class="grid gap-3 sm:grid-cols-5">
        @foreach ($steps as $index => $label)
            <button type="button" wire:click="goToStep({{ $index + 1 }})" class="rounded-2xl border px-4 py-3 text-sm font-medium {{ $step === $index + 1 ? 'border-trust-700 bg-trust-700 text-white' : 'border-slate-200 bg-white text-slate-600' }}">{{ $label }}</button>
        @endforeach
    </div>

    <x-card class="space-y-6">
        @if ($statusMessage)
            <x-alert variant="success">{{ $statusMessage }}</x-alert>
        @endif

        @if ($step === 1)
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model.live="title" name="title" label="Naslov aukcije" placeholder="Rolex Oyster Perpetual 39" />
                <x-select wire:model.live="categoryId" name="category" label="Kategorija" :options="$categoryOptions" />
            </div>
            <x-input wire:model.live="description" name="description" type="textarea" label="Opis artikla" hint="Jasno opiši stanje, dodatke i eventualne nedostatke.">Vrlo dobro očuvan sat sa originalnom kutijom.</x-input>
        @elseif ($step === 2)
            <x-alert variant="info">Možeš dodati do 10 URL-ova slika i promijeniti njihov redoslijed prije objave.</x-alert>
            @error('imageUrls')
                <x-alert variant="danger">{{ $message }}</x-alert>
            @enderror
            <div class="grid gap-4 sm:grid-cols-[1fr_auto]">
                <x-input wire:model.live="newImageUrl" name="image_url" label="URL slike" placeholder="https://..." />
                <div class="pt-8">
                    <x-button wire:click="addImage">Dodaj sliku</x-button>
                </div>
            </div>
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6">
                <label class="block text-sm font-medium text-slate-700">Upload slike</label>
                <input wire:model.live="uploadedImages" type="file" multiple accept="image/*" class="mt-3 block w-full text-sm text-slate-600">
                <p class="mt-2 text-xs text-slate-500">Maksimalno 10 slika, do 2 MB po slici.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                @forelse ($imageUrls as $index => $url)
                    <div class="space-y-3 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="overflow-hidden rounded-2xl bg-white">
                            <img src="{{ $url }}" alt="Auction image {{ $index + 1 }}" class="h-40 w-full object-cover" loading="lazy" />
                        </div>
                        <div class="rounded-2xl bg-white p-4 text-xs text-slate-500">{{ $url }}</div>
                        <div class="flex gap-2">
                            <x-button variant="ghost" wire:click="moveImage({{ $index }}, 'up')">↑</x-button>
                            <x-button variant="ghost" wire:click="moveImage({{ $index }}, 'down')">↓</x-button>
                            <x-button variant="danger" wire:click="removeImage({{ $index }})">Ukloni</x-button>
                        </div>
                    </div>
                @empty
                    @for ($i = 0; $i < 3; $i++)
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500">Slot za sliku</div>
                    @endfor
                @endforelse
            </div>
        @elseif ($step === 3)
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input wire:model.live="startPrice" name="start_price" type="number" label="Startna cijena" />
                <x-input wire:model.live="reservePrice" name="reserve_price" type="number" label="Reserve cijena" />
                <x-input wire:model.live="buyNowPrice" name="buy_now" type="number" label="Kupi odmah" />
            </div>
            <x-select wire:model.live="durationDays" name="duration_days" label="Trajanje aukcije" :options="collect($durationOptions)->mapWithKeys(fn($d) => [$d => $d.' '.($d === 1 ? 'dan' : 'dana')])->all()" />
        @elseif ($step === 4)
            <div class="grid gap-4 sm:grid-cols-2">
                <x-select wire:model.live="shippingMethod" name="shipping_method" label="Dostava" :options="['' => 'Odaberi dostavu', 'euroexpress' => 'EuroExpress', 'postexpress' => 'PostExpress', 'bhposta' => 'BH Pošta', 'pickup' => 'Lično preuzimanje']" />
                <x-input wire:model.live="shippingPrice" name="shipping_price" type="number" label="Cijena dostave" />
            </div>
            <x-input wire:model.live="location" name="location" label="Lokacija slanja" placeholder="Sarajevo" />
        @else
            <x-alert variant="success">Pregled je spreman za draft ili objavu. Provjeri slike, cijenu i dostavu prije zadnjeg koraka.</x-alert>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-price-display :amount="$startPrice !== '' ? $startPrice : '1250.00'">Startna cijena</x-price-display>
                <div>
                    <p class="price-label">Trajanje</p>
                    <p class="price">{{ $durationDays }} {{ $durationDays === 1 ? 'dan' : 'dana' }}</p>
                </div>
                <div>
                    <p class="price-label">Dostava</p>
                    <p class="price">{{ $shippingMethod !== '' ? $shippingMethod : 'nije odabrana' }}</p>
                </div>
                <div>
                    <p class="price-label">Lokacija</p>
                    <p class="price">{{ $location !== '' ? $location : 'nije postavljena' }}</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach (array_slice($imageUrls, 0, 3) as $url)
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                        <img src="{{ $url }}" alt="Preview image" class="h-40 w-full object-cover" loading="lazy" />
                    </div>
                @endforeach
            </div>
        @endif

        <x-alert variant="info">Free tier limit: 5 aktivnih aukcija. Premium otključava 50 aktivnih listinga i nižu komisiju.</x-alert>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
            <x-button variant="ghost" wire:click="previousStep" :disabled="$step === 1">Nazad</x-button>
            <div class="flex flex-col gap-3 sm:flex-row">
                <x-button variant="ghost" wire:click="saveDraft">Sačuvaj draft</x-button>
                @if ($step < count($steps))
                    <x-button wire:click="nextStep">Sljedeći korak</x-button>
                @else
                    <x-button wire:click="publish">Objavi aukciju</x-button>
                @endif
            </div>
        </div>
    </x-card>
</div>
