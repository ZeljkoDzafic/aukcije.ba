<?php
namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\WalletFrozenException;
use App\Models\{Order, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;

class EscrowService
{
    protected WalletService $walletService;

    public function __construct(?WalletService $walletService = null)
    {
        $this->walletService = $walletService ?? app(WalletService::class);
    }

    /**
     * Freeze buyer funds when they win an auction.
     * Deducts from wallet balance and records escrow hold.
     */
    public function holdFunds(Order $order): bool
    {
        try {
            DB::transaction(function () use ($order) {
            $wallet = $this->walletService->getWallet($order->buyer);

            $total = (float) ($order->total_amount ?? $order->amount ?? 0);

            if ($wallet->balance < $total) {
                throw new InsufficientFundsException($total, (float) $wallet->balance);
            }

            // Deduct from available balance and add to escrow_balance
            $wallet->decrement('balance', $total);
            $wallet->increment('escrow_balance', $total);

            WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'user_id'        => $order->buyer_id,
                'type'           => 'escrow_hold',
                'amount'         => -$total,
                'balance_after'  => $wallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Escrow hold za narudžbu #{$order->id}",
            ]);
            });
        } catch (InsufficientFundsException) {
            return false;
        }

        return true;
    }

    /**
     * Release escrow funds to seller (minus commission).
     */
    public function releaseFunds(Order $order): bool
    {
        $buyerWallet = $this->walletService->getWallet($order->buyer);
        $total = (float) ($order->total_amount ?? $order->amount ?? 0);

        if ($buyerWallet->escrow_balance < $total || $total <= 0) {
            return false;
        }

        DB::transaction(function () use ($order, $buyerWallet, $total) {
            $sellerWallet = $this->walletService->getWallet($order->seller);

            $commissionRate = (float) $order->seller->getCommissionRate();
            $commission = round($total * $commissionRate, 2);
            $sellerNet  = round($total - $commission, 2);

            // Release from escrow
            $buyerWallet->decrement('escrow_balance', $total);

            WalletTransaction::create([
                'wallet_id'      => $buyerWallet->id,
                'user_id'        => $order->buyer_id,
                'type'           => 'escrow_release',
                'amount'         => -$total,
                'balance_after'  => $buyerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Escrow release za narudžbu #{$order->id}",
            ]);

            // Pay seller (net of commission)
            $sellerWallet->increment('balance', $sellerNet);

            WalletTransaction::create([
                'wallet_id'      => $sellerWallet->id,
                'user_id'        => $order->seller_id,
                'type'           => 'escrow_release',
                'amount'         => $sellerNet,
                'balance_after'  => $sellerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Prihod od prodaje #{$order->id} (komisija: {$commission} BAM)",
            ]);

            // Record commission deduction
            WalletTransaction::create([
                'wallet_id'      => $sellerWallet->id,
                'user_id'        => $order->seller_id,
                'type'           => 'commission',
                'amount'         => -$commission,
                'balance_after'  => $sellerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Platforma komisija ({$commission} BAM)",
            ]);
            $order->update(['status' => 'completed']);
        });

        return true;
    }

    /**
     * Refund buyer (full or partial).
     */
    public function refundBuyer(Order $order, float $amount): bool
    {
        DB::transaction(function () use ($order, $amount) {
            $buyerWallet = $this->walletService->getWallet($order->buyer);

            $buyerWallet->decrement('escrow_balance', $amount);
            $buyerWallet->increment('balance', $amount);

            WalletTransaction::create([
                'wallet_id'      => $buyerWallet->id,
                'user_id'        => $order->buyer_id,
                'type'           => 'refund',
                'amount'         => $amount,
                'balance_after'  => $buyerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Refund za narudžbu #{$order->id}",
            ]);
        });

        return true;
    }

    /**
     * Auto-release escrow for orders delivered 14+ days ago with no open dispute.
     * Called by scheduler: escrow:auto-release
     */
    public function autoRelease(): int
    {
        $releaseDays = config('escrow.auto_release_days', 14);

        $orders = \App\Models\Order::where('status', 'delivered')
            ->where('delivered_at', '<=', now()->subDays($releaseDays))
            ->whereDoesntHave('dispute', fn ($q) => $q->whereIn('status', ['open', 'in_review']))
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            $this->releaseFunds($order);
            $order->update(['status' => 'completed']);
            $count++;
        }

        return $count;
    }
}
