@php
    $walletCards = [
        ['Raspoloživo', $this->wallet ? number_format((float) $this->wallet->available_balance, 2, ',', '.').' BAM' : '420,00 BAM'],
        ['U escrowu', $this->wallet ? number_format((float) $this->wallet->escrow_balance, 2, ',', '.').' BAM' : '180,00 BAM'],
        ['Ukupno', $this->wallet ? number_format((float) $this->wallet->total_balance, 2, ',', '.').' BAM' : '600,00 BAM'],
    ];
@endphp

<div class="space-y-8">
    @if ($feedback)
        <x-alert variant="success">{{ $feedback }}</x-alert>
    @endif

    @if ($errorMessage)
        <x-alert variant="danger">{{ $errorMessage }}</x-alert>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($walletCards as [$label, $value])
            <x-card>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <x-card class="space-y-4">
            <h1 class="text-2xl font-semibold text-slate-900">Dopuni wallet</h1>
            <div class="grid grid-cols-2 gap-3">
                @foreach ([10, 25, 50, 100] as $amount)
                    <button type="button" wire:click="quickDeposit({{ $amount }})" class="rounded-2xl border border-slate-200 px-4 py-3 text-left font-medium text-slate-700 hover:border-trust-300 hover:bg-trust-50">{{ $amount }} BAM</button>
                @endforeach
            </div>
            <x-input wire:model.live="depositAmount" name="deposit_amount" type="number" label="Custom iznos" />
            <x-select wire:model.live="gateway" name="gateway" label="Gateway" :options="['stripe' => 'Stripe', 'monri' => 'Monri', 'corvus' => 'CorvusPay', 'wallet' => 'Wallet transfer']" />
            <x-button wire:click="deposit">Dopuni balans</x-button>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Isplata</h2>
            <x-input wire:model.live="withdrawAmount" name="withdraw_amount" type="number" label="Iznos za isplatu" />
            <x-alert variant="info">Minimalna isplata je 10 BAM. Isplate veće od 500 BAM mogu tražiti admin odobrenje.</x-alert>
            <x-button variant="secondary" wire:click="withdraw">Pošalji zahtjev za isplatu</x-button>
        </x-card>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <x-card class="space-y-4">
            <h2 class="text-2xl font-semibold text-slate-900">Status i objašnjenja</h2>
            <x-alert variant="warning">{{ $this->wallet ? number_format((float) $this->wallet->escrow_balance, 2, ',', '.') : '180,00' }} BAM je trenutno u escrowu.</x-alert>
            <x-alert variant="info">Ako balans nije dovoljan za uplatu narudžbe, platforma će tražiti dopunu walleta ili kartično plaćanje.</x-alert>
        </x-card>

        <x-card class="space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Historija transakcija</h2>
                    <p class="text-sm text-slate-500">Filtrirano po tipu, sa BHS oznakama i ikonama statusa.</p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    @foreach (['all' => 'Sve', 'deposit' => 'Depozit', 'withdrawal' => 'Isplata', 'escrow_hold' => 'Escrow'] as $key => $label)
                        <button type="button" wire:click="$set('filter', '{{ $key }}')" class="rounded-full px-4 py-2 {{ $filter === $key ? 'bg-trust-50 text-trust-700' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <x-data-table :headers="['Tip', 'Opis', 'Iznos', 'Status']">
                @forelse ($this->transactions as $transaction)
                    <tr class="table-row">
                        <td class="px-4 py-3">{{ $transaction->type_icon }} {{ $transaction->type_label }}</td>
                        <td class="px-4 py-3">{{ $transaction->description ?: 'Wallet transakcija' }}</td>
                        <td class="px-4 py-3 font-medium {{ $transaction->isPositive() ? 'text-emerald-700' : 'text-slate-900' }}">
                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format((float) $transaction->amount, 2, ',', '.') }} BAM
                        </td>
                        <td class="px-4 py-3"><x-badge :variant="$transaction->isPositive() ? 'success' : 'info'">Evidentirano</x-badge></td>
                    </tr>
                @empty
                    <tr class="table-row">
                        <td class="px-4 py-3">↑ Depozit</td>
                        <td class="px-4 py-3">Stripe dopuna walleta</td>
                        <td class="px-4 py-3 font-medium text-emerald-700">+100,00 BAM</td>
                        <td class="px-4 py-3"><x-badge variant="success">Završeno</x-badge></td>
                    </tr>
                @endforelse
            </x-data-table>
        </x-card>
    </div>
</div>
