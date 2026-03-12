<div class="space-y-6">
    <x-card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-4">
            <x-input wire:model.live.debounce.300ms="search" name="search" label="Pretraga" placeholder="Ime ili email" />
            <x-select wire:model.live="role" name="role" label="Rola" :options="['' => 'Sve role', 'buyer' => 'Buyer', 'seller' => 'Seller', 'moderator' => 'Moderator']" />
            <x-select wire:model.live="kyc" name="kyc" label="KYC status" :options="['' => 'Svi nivoi', '1' => 'Nivo 1', '2' => 'Nivo 2', '3' => 'Nivo 3']" />
            <x-select wire:model.live="tier" name="tier" label="Tier" :options="['' => 'Svi tieri', 'free' => 'Free', 'premium' => 'Premium', 'storefront' => 'Storefront']" />
        </div>
        @if ($statusMessage)
            <x-alert variant="info">{{ $statusMessage }}</x-alert>
        @endif
    </x-card>

    <x-card class="space-y-4">
        <x-data-table :headers="['Korisnik', 'Rola', 'KYC', 'Tier', 'Akcije']">
            @foreach ($this->filteredUsers as $user)
                <tr class="table-row">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $user['name'] }}</td>
                    <td class="px-4 py-3">{{ $user['role'] }}</td>
                    <td class="px-4 py-3">{{ $user['kyc'] }}</td>
                    <td class="px-4 py-3">{{ $user['tier'] }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <x-button variant="ghost" :href="route('admin.users.show', ['user' => $user['id']])">Pregled</x-button>
                            <x-button variant="secondary" wire:click="moderate({{ $user['id'] }}, 'change-role')">Rola</x-button>
                            <x-button variant="danger" wire:click="moderate({{ $user['id'] }}, 'ban')">Ban / Unban</x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </x-card>
</div>
