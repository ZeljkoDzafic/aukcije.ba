<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerification;
use App\Services\KycService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * T-1501: KYC backoffice — admin reviews uploaded identity documents.
 */
class KycBackofficeController extends Controller
{
    public function __construct(private readonly KycService $kycService) {}

    /**
     * List users with pending KYC documents.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->get('status', 'pending');

        $verifications = UserVerification::query()
            ->where('status', $status)
            ->with(['user:id,name,email,kyc_level'])
            ->orderBy('created_at')
            ->paginate(30);

        return response()->json([
            'data' => $verifications->items(),
            'meta' => [
                'current_page' => $verifications->currentPage(),
                'last_page'    => $verifications->lastPage(),
                'total'        => $verifications->total(),
            ],
        ]);
    }

    /**
     * Show a single user's KYC verifications and documents.
     */
    public function show(User $user): JsonResponse
    {
        $verifications = $user->verifications()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => [
                'user'          => $user->only(['id', 'name', 'email', 'kyc_level']),
                'verifications' => $verifications,
                'kyc_level'     => $this->kycService->getVerificationLevel($user),
            ],
        ]);
    }

    /**
     * Approve a KYC document submission.
     */
    public function approve(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'type'  => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $verification = $this->kycService->reviewDocument(
            $user,
            $data['type'],
            'approved',
            $data['notes'] ?? null,
            Auth::user(),
        );

        return response()->json(['data' => $verification]);
    }

    /**
     * Reject a KYC document submission.
     */
    public function reject(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'type'  => ['required', 'string'],
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $verification = $this->kycService->reviewDocument(
            $user,
            $data['type'],
            'rejected',
            $data['notes'],
            Auth::user(),
        );

        return response()->json(['data' => $verification]);
    }
}
