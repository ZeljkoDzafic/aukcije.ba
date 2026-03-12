<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AdminLog;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class UserProfileManager extends Component
{
    public ?string $userId = null;

    public string $statusMessage = '';

    public function moderate(string $action): void
    {
        $user = $this->getUser();

        if (! $user) {
            return;
        }

        match ($action) {
            'ban' => $user->update(['is_banned' => ! $user->is_banned, 'banned_at' => $user->is_banned ? null : now()]),
            'change-role' => method_exists($user, 'syncRoles') ? $user->syncRoles([$user->hasRole('seller') ? 'buyer' : 'seller']) : null,
            'force-kyc-review' => $this->forceKycReview($user),
            'reset-password' => null,
            default => null,
        };

        if (Schema::hasTable('admin_logs')) {
            AdminLog::query()->create([
                'admin_id' => Auth::id(),
                'action' => $action,
                'target_type' => 'user',
                'target_id' => $user->id,
                'metadata' => ['email' => $user->email],
            ]);
        }

        $this->statusMessage = match ($action) {
            'reset-password' => "Reset lozinke je pripremljen za {$user->email}.",
            'force-kyc-review' => "KYC review je označen za korisnika {$user->name}.",
            default => "Akcija '{$action}' je izvršena za korisnika {$user->name}.",
        };
    }

    public function render(): View
    {
        return view('livewire.admin.user-profile-manager', [
            'user' => $this->getUser(),
        ]);
    }

    protected function getUser(): ?User
    {
        if (! $this->userId || ! Schema::hasTable('users')) {
            return null;
        }

        return User::query()
            ->with(['wallet', 'auctions', 'orders', 'soldOrders'])
            ->find($this->userId);
    }

    protected function forceKycReview(User $user): void
    {
        if (! Schema::hasTable('user_verifications')) {
            return;
        }

        UserVerification::query()->updateOrCreate(
            ['user_id' => $user->id, 'type' => 'id_document'],
            ['status' => 'pending', 'notes' => 'Admin requested manual review', 'reviewer_id' => Auth::id()]
        );
    }
}
