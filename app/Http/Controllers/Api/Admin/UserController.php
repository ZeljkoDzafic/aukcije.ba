<?php

declare(strict_types=1);

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
            ->when($request->filled('search'), fn ($q) => $q
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('email', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('is_banned'), fn ($q) => $q->where('is_banned', filter_var($request->input('is_banned'), FILTER_VALIDATE_BOOLEAN)))
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data'    => $users->items(),
            'meta'    => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->loadCount(['auctions', 'orders'])
            ->load([
                'wallet',
                'auctions'    => fn ($q) => $q->latest()->limit(5),
                'orders'      => fn ($q) => $q->latest()->limit(5),
                'soldOrders'  => fn ($q) => $q->latest()->limit(5),
            ]);

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string'],
        ]);

        match ($validated['role']) {
            'buyer'           => $user->revokeSellerAccess(),
            'seller'          => $user->grantSellerAccess(),
            'verified_seller' => $user->grantSellerAccess(true),
            default           => method_exists($user, 'syncRoles') ? $user->syncRoles([$validated['role']]) : null,
        };

        AdminLog::query()->create([
            'admin_id'    => $request->user()->id,
            'action'      => 'update-role',
            'target_type' => 'user',
            'target_id'   => $user->id,
            'metadata'    => ['role' => $validated['role']],
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id, 'roles' => $user->fresh()->marketplaceRoles()]]);
    }

    public function ban(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'is_banned'  => ! $user->is_banned,
            'banned_at'  => $user->is_banned ? null : now(),
            'ban_reason' => $validated['reason'] ?? null,
        ]);

        AdminLog::query()->create([
            'admin_id'    => $request->user()->id,
            'action'      => $user->fresh()->is_banned ? 'ban' : 'unban',
            'target_type' => 'user',
            'target_id'   => $user->id,
            'metadata'    => ['reason' => $validated['reason'] ?? null],
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id, 'is_banned' => $user->fresh()->is_banned]]);
    }
}
