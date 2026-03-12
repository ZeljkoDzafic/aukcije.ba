<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * @param  \App\Models\Wallet|null  $wallet
     * @return array<string, mixed>|null
     */
    protected function walletPayload($wallet): ?array
    {
        if (! $wallet) {
            return null;
        }

        return [
            'id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'balance' => number_format((float) $wallet->balance, 2, '.', ''),
            'escrow_balance' => number_format((float) $wallet->escrow_balance, 2, '.', ''),
            'frozen' => (bool) $wallet->frozen,
            'frozen_at' => $wallet->frozen_at,
            'frozen_reason' => $wallet->frozen_reason,
            'created_at' => $wallet->created_at,
            'updated_at' => $wallet->updated_at,
        ];
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile', 'wallet');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $user->profile,
                'wallet' => $this->walletPayload($user->wallet),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:50'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
            'full_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->fill(array_filter([
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ], fn ($value) => $value !== null))->save();

        if (Schema::hasTable('user_profiles')) {
            $profile = UserProfile::query()->firstOrCreate(['user_id' => $user->id]);

            $profile->fill(array_filter([
                'full_name' => $validated['full_name'] ?? ($validated['name'] ?? null),
                'bio' => $validated['bio'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'avatar_url' => $validated['avatar_url'] ?? null,
            ], fn ($value) => $value !== null))->save();
        }

        $user = $user->fresh()->load('profile', 'wallet');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $user->profile,
                'wallet' => $this->walletPayload($user->wallet),
            ],
        ]);
    }
}
