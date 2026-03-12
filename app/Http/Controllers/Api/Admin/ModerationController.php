<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\BulkModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * T-1500: Bulk auction moderation endpoints for admins.
 */
class ModerationController extends Controller
{
    public function __construct(private readonly BulkModerationService $moderationService) {}

    public function bulkApprove(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:100'],
            'auction_ids.*' => ['required', 'uuid'],
            'note'          => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->moderationService->approve(
            Auth::user(),
            $data['auction_ids'],
            $data['note'] ?? null,
        );

        return response()->json(['data' => $result]);
    }

    public function bulkReject(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:100'],
            'auction_ids.*' => ['required', 'uuid'],
            'reason'        => ['required', 'string', 'max:500'],
        ]);

        $result = $this->moderationService->reject(
            Auth::user(),
            $data['auction_ids'],
            $data['reason'],
        );

        return response()->json(['data' => $result]);
    }

    public function bulkFeature(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:100'],
            'auction_ids.*' => ['required', 'uuid'],
            'featured'      => ['required', 'boolean'],
        ]);

        $count = $this->moderationService->setFeatured(
            Auth::user(),
            $data['auction_ids'],
            $data['featured'],
        );

        return response()->json(['data' => ['updated' => $count]]);
    }
}
