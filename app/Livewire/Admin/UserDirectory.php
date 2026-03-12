<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\User;
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

    /** @var list<array{id: string, name: string, role: string, kyc: string, tier: string}> */
    public array $users = [
        ['id' => '1', 'name' => 'Amar Hadžić', 'role' => 'seller', 'kyc' => '3', 'tier' => 'premium'],
        ['id' => '2', 'name' => 'Lana R.', 'role' => 'buyer', 'kyc' => '1', 'tier' => '-'],
        ['id' => '3', 'name' => 'Admin Demo', 'role' => 'moderator', 'kyc' => '3', 'tier' => '-'],
    ];

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

                if ($action === 'change-role' && method_exists($model, 'syncRoles')) {
                    $nextRole = $model->hasRole('seller') ? 'buyer' : 'seller';
                    $model->syncRoles([$nextRole]);
                }

                if (Schema::hasTable('admin_logs')) {
                    AdminLog::query()->create([
                        'admin_id' => Auth::id(),
                        'action' => $action,
                        'target_type' => 'user',
                        'target_id' => $model->id,
                        'metadata' => ['name' => $model->name],
                    ]);
                }

                $this->statusMessage = "Akcija '{$action}' izvršena za korisnika {$model->name}.";

                return;
            }
        }

        $this->statusMessage = $user ? "Akcija '{$action}' pripremljena za korisnika {$user['name']}." : '';
    }

    /**
     * @return Collection<int, array{id: string, name: string, role: string, kyc: string, tier: string}>
     */
    public function getFilteredUsersProperty(): Collection
    {
        $users = collect($this->users);

        if (Schema::hasTable('users')) {
            $databaseUsers = User::query()
                ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->limit(20)
                ->get()
                ->map(function (User $user): array {
                    $role = method_exists($user, 'getRoleNames') ? ($user->getRoleNames()->first() ?? 'buyer') : 'buyer';
                    $isSeller = method_exists($user, 'hasRole') && ($user->hasRole('seller') || $user->hasRole('verified_seller'));

                    return [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                        'role' => $role,
                        'kyc' => (string) $user->kycLevel(),
                        'tier' => $isSeller ? ($user->getTier()['name'] ?? 'seller') : '-',
                    ];
                });

            if ($databaseUsers->isNotEmpty()) {
                $users = $databaseUsers;
            }
        }

        return $users
            ->filter(fn (array $user) => $this->search === '' || str_contains(strtolower($user['name']), strtolower($this->search)))
            ->filter(fn (array $user) => $this->role === '' || $user['role'] === $this->role)
            ->filter(fn (array $user) => $this->kyc === '' || $user['kyc'] === $this->kyc)
            ->filter(fn (array $user) => $this->tier === '' || $user['tier'] === $this->tier)
            ->values();
    }

    public function render(): View
    {
        return view('livewire.admin.user-directory');
    }
}
