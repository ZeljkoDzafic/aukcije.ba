<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class UserDirectory extends Component
{
    public string $search = '';

    public string $role = '';

    public string $statusMessage = '';

    public string $kyc = '';

    public string $tier = '';

    public string $bulkNote = 'Admin bulk odluka nad korisničkom queue listom.';

    /** @var list<string> */
    public array $selectedUserIds = [];

    /** @var list<array{id: string, name: string, role: string, kyc: string, tier: string, latest_note?: string|null}> */
    public array $users = [
        ['id' => '1', 'name' => 'Aleksa K.', 'role' => 'kupac, prodavac', 'kyc' => '3', 'tier' => 'premium', 'latest_note' => 'KYC kompletiran.'],
        ['id' => '2', 'name' => 'Nika R.', 'role' => 'kupac', 'kyc' => '1', 'tier' => '-', 'latest_note' => 'Nizak KYC nivo.'],
        ['id' => '3', 'name' => 'Admin Demo', 'role' => 'moderator', 'kyc' => '3', 'tier' => '-', 'latest_note' => null],
    ];

    public function toggleSelection(string $userId): void
    {
        if (in_array($userId, $this->selectedUserIds, true)) {
            $this->selectedUserIds = array_values(array_filter(
                $this->selectedUserIds,
                fn (string $selectedId): bool => $selectedId !== $userId
            ));

            return;
        }

        $this->selectedUserIds[] = $userId;
    }

    public function selectFiltered(): void
    {
        $this->selectedUserIds = $this->filteredUsers->pluck('id')->values()->all();
    }

    public function clearSelection(): void
    {
        $this->selectedUserIds = [];
    }

    public function moderate(int $userId, string $action): void
    {
        $user = collect($this->users)->firstWhere('id', $userId);

        if (Schema::hasTable('users')) {
            $model = User::query()->find($userId);

            if ($model) {
                if ($action === 'ban') {
                    $model->update([
                        'is_banned' => ! $model->is_banned,
                        'banned_at' => $model->is_banned ? null : now(),
                    ]);
                }

                if ($action === 'toggle-seller-access') {
                    $model->hasAnyRole(['seller', 'verified_seller'])
                        ? $model->revokeSellerAccess()
                        : $model->grantSellerAccess();
                }

                app(AdminAuditService::class)->record(
                    Auth::id(),
                    $action,
                    'user',
                    $model->id,
                    [
                        'name' => $model->name,
                        'note' => $this->bulkNote !== '' ? $this->bulkNote : null,
                    ]
                );

                $this->statusMessage = "Akcija '{$action}' izvršena za korisnika {$model->name}.";

                return;
            }
        }

        $this->statusMessage = $user ? "Akcija '{$action}' pripremljena za korisnika {$user['name']}." : '';
    }

    public function applyBulk(string $action): void
    {
        $selectedIds = $this->selectedUserIds !== []
            ? $this->selectedUserIds
            : $this->filteredUsers->pluck('id')->all();

        if ($selectedIds === []) {
            $this->statusMessage = 'Nema korisnika za bulk akciju.';

            return;
        }

        foreach ($selectedIds as $userId) {
            $model = Schema::hasTable('users') ? User::query()->find($userId) : null;

            if ($model) {
                match ($action) {
                    'ban-selected' => $model->update([
                        'is_banned' => true,
                        'banned_at' => $model->banned_at ?? now(),
                    ]),
                    'force-kyc-selected' => $this->forceKycReview($model),
                    'grant-seller-selected' => $model->grantSellerAccess(),
                    default => null,
                };

                app(AdminAuditService::class)->record(
                    Auth::id(),
                    $action,
                    'user',
                    $model->id,
                    [
                        'name' => $model->name,
                        'note' => $this->bulkNote,
                    ]
                );
            }
        }

        $this->clearSelection();

        $this->statusMessage = match ($action) {
            'ban-selected' => 'Bulk ban je izvršen nad označenim korisnicima.',
            'force-kyc-selected' => 'Bulk KYC review je označen nad označenim korisnicima.',
            default => 'Bulk seller pristup je ažuriran za označene korisnike.',
        };
    }

    /**
     * @return Collection<int, array{id: string, name: string, role: string, kyc: string, tier: string, latest_note?: string|null}>
     */
    public function getFilteredUsersProperty(): Collection
    {
        $users = collect($this->users);
        $latestNotes = collect();

        if (Schema::hasTable('users')) {
            if (Schema::hasTable('admin_logs')) {
                $latestNotes = \App\Models\AdminLog::query()
                    ->where('target_type', 'user')
                    ->latest('created_at')
                    ->get()
                    ->groupBy('target_id')
                    ->map(fn (Collection $entries): ?string => $entries->first()?->metadata['note'] ?? null);
            }

            $databaseUsers = User::query()
                ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->limit(20)
                ->get()
                ->map(function (User $user) use ($latestNotes): array {
                    $role = method_exists($user, 'roleSummary') ? $user->roleSummary() : 'buyer';
                    $isSeller = method_exists($user, 'hasRole') && ($user->hasRole('seller') || $user->hasRole('verified_seller'));

                    return [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                        'role' => $role,
                        'kyc' => (string) $user->kycLevel(),
                        'tier' => $isSeller ? ($user->getTier()['name'] ?? 'seller') : '-',
                        'latest_note' => $latestNotes->get($user->id),
                    ];
                });

            if ($databaseUsers->isNotEmpty()) {
                $users = $databaseUsers;
            }
        }

        return $users
            ->filter(fn (array $user) => $this->search === '' || str_contains(strtolower($user['name']), strtolower($this->search)))
            ->filter(fn (array $user) => $this->role === '' || str_contains($user['role'], $this->role))
            ->filter(fn (array $user) => $this->kyc === '' || $user['kyc'] === $this->kyc)
            ->filter(fn (array $user) => $this->tier === '' || $user['tier'] === $this->tier)
            ->values();
    }

    public function render(): View
    {
        return view('livewire.admin.user-directory');
    }
}
