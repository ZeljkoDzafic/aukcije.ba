<div class="space-y-6">
    <x-card class="panel-shell space-y-4">
        <div class="grid gap-4 md:grid-cols-4">
            <x-input wire:model.live.debounce.300ms="search" name="search" label="Pretraga" placeholder="Ime ili email" />
            <x-select wire:model.live="role" name="role" label="Rola" :options="['' => 'Sve role', 'buyer' => 'Buyer', 'seller' => 'Seller', 'verified seller' => 'Verified Seller', 'moderator' => 'Moderator', 'super admin' => 'Super Admin']" />
            <x-select wire:model.live="kyc" name="kyc" label="KYC status" :options="['' => 'Svi nivoi', '1' => 'Nivo 1', '2' => 'Nivo 2', '3' => 'Nivo 3']" />
            <x-select wire:model.live="tier" name="tier" label="Tier" :options="['' => 'Svi tieri', 'free' => 'Free', 'premium' => 'Premium', 'storefront' => 'Storefront']" />
        </div>
        <div class="flex flex-wrap gap-3">
            <x-button variant="ghost" wire:click="selectFiltered">Označi filtrirane</x-button>
            <x-button variant="ghost" wire:click="clearSelection">Poništi izbor</x-button>
            <x-button variant="danger" wire:click="applyBulk('ban-selected')">Ban selected</x-button>
            <x-button variant="secondary" wire:click="applyBulk('force-kyc-selected')">Force KYC selected</x-button>
            <x-button variant="secondary" wire:click="applyBulk('grant-seller-selected')">Seller pristup selected</x-button>
        </div>
        <x-input wire:model.live="bulkNote" name="user_bulk_note" type="textarea" label="Bulk decision note" />
        @if ($selectedUserIds !== [])
            <x-alert variant="info">{{ count($selectedUserIds) }} korisnika je označeno za bulk akciju.</x-alert>
        @endif
        @if ($statusMessage)
            <x-alert variant="info">{{ $statusMessage }}</x-alert>
        @endif
    </x-card>

    <x-card class="panel-shell space-y-4">
        <x-data-table :headers="['Izbor', 'Korisnik', 'Rola', 'KYC', 'Tier', 'Napomena', 'Akcije']">
            @foreach ($this->filteredUsers as $user)
                <tr class="table-row">
                    <td class="px-4 py-3">
                        <input
                            type="checkbox"
                            wire:click="toggleSelection('{{ $user['id'] }}')"
                            @checked(in_array($user['id'], $selectedUserIds, true))
                            class="h-4 w-4 rounded border-slate-300 text-trust-600 focus:ring-trust-500"
                        />
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $user['name'] }}</td>
                    <td class="px-4 py-3">{{ $user['role'] }}</td>
                    <td class="px-4 py-3">{{ $user['kyc'] }}</td>
                    <td class="px-4 py-3">{{ $user['tier'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $user['latest_note'] ?? 'Nema bilješke' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <x-button variant="ghost" :href="route('admin.users.show', ['user' => $user['id']])">Pregled</x-button>
                            <x-button variant="secondary" wire:click="moderate({{ $user['id'] }}, 'toggle-seller-access')">Seller pristup</x-button>
                            <x-button variant="danger" wire:click="moderate({{ $user['id'] }}, 'ban')">Ban / Unban</x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </x-card>
</div>
