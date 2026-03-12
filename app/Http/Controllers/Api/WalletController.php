<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    public function balance(Request $request): JsonResponse
    {
        $data = $this->walletService->getBalanceSummary($request->user());

        return response()->json(array_merge(['success' => true], $data, [
            'data' => $data,
            'balance' => (float) $data['available'],
        ]));
    }

    public function deposit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'gateway' => ['required', 'string', 'max:50'],
            'reference_id' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $transaction = $this->walletService->deposit(
                $request->user(),
                (float) $validated['amount'],
                $validated['gateway'],
                $validated['reference_id'] ?? null
            );
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wallet dopuna je evidentirana.',
            'data' => $transaction,
        ]);
    }

    public function withdraw(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $transaction = $this->walletService->withdraw($request->user(), (float) $validated['amount']);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Zahtjev za isplatu je evidentiran.',
            'data' => $transaction,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $wallet = $this->walletService->getWallet($request->user());

        return response()->json([
            'success' => true,
            'data' => $wallet->transactions()->latest('created_at')->get(),
        ]);
    }
}
