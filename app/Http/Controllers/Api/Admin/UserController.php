<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['wallet', 'auctions', 'orders', 'soldOrders']);

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string'],
        ]);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }

        AdminLog::query()->create([
            'admin_id' => $request->user()->id,
            'action' => 'update-role',
            'target_type' => 'user',
            'target_id' => $user->id,
            'metadata' => ['role' => $validated['role']],
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id, 'role' => $validated['role']]]);
    }

    public function ban(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'is_banned' => ! $user->is_banned,
            'banned_at' => $user->is_banned ? null : now(),
            'ban_reason' => $validated['reason'] ?? null,
        ]);

        AdminLog::query()->create([
            'admin_id' => $request->user()->id,
            'action' => $user->fresh()->is_banned ? 'ban' : 'unban',
            'target_type' => 'user',
            'target_id' => $user->id,
            'metadata' => ['reason' => $validated['reason'] ?? null],
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id, 'is_banned' => $user->fresh()->is_banned]]);
    }
}
