<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile', 'wallet');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $user->profile,
                'wallet' => $user->wallet,
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
                'wallet' => $user->wallet,
            ],
        ]);
    }
}
