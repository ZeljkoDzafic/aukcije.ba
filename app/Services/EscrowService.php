<?php
namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\WalletFrozenException;
use App\Models\{Order, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;

class EscrowService
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Freeze buyer funds when they win an auction.
     * Deducts from wallet balance and records escrow hold.
     */
    public function holdFunds(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $wallet = $this->walletService->getWallet($order->buyer);

            $total = $order->amount + ($order->shipping_cost ?? 0);

            if ($wallet->balance < $total) {
                throw new InsufficientFundsException($total, $wallet->balance);
            }

            // Deduct from available balance and add to escrow_balance
            $wallet->decrement('balance', $total);
            $wallet->increment('escrow_balance', $total);

            WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'escrow_hold',
                'amount'         => -$total,
                'balance_after'  => $wallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Escrow hold za narudžbu #{$order->id}",
            ]);
        });
    }

    /**
     * Release escrow funds to seller (minus commission).
     */
    public function releaseFunds(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $buyerWallet  = $this->walletService->getWallet($order->buyer);
            $sellerWallet = $this->walletService->getWallet($order->seller);

            $total      = $order->amount;
            $commission = $order->commission;
            $sellerNet  = $total - $commission;

            // Release from escrow
            $buyerWallet->decrement('escrow_balance', $total);

            WalletTransaction::create([
                'wallet_id'      => $buyerWallet->id,
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
                'type'           => 'commission',
                'amount'         => -$commission,
                'balance_after'  => $sellerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Platforma komisija ({$commission} BAM)",
            ]);
        });
    }

    /**
     * Refund buyer (full or partial).
     */
    public function refundBuyer(Order $order, float $amount): void
    {
        DB::transaction(function () use ($order, $amount) {
            $buyerWallet = $this->walletService->getWallet($order->buyer);

            $buyerWallet->decrement('escrow_balance', $amount);
            $buyerWallet->increment('balance', $amount);

            WalletTransaction::create([
                'wallet_id'      => $buyerWallet->id,
                'type'           => 'refund',
                'amount'         => $amount,
                'balance_after'  => $buyerWallet->fresh()->balance,
                'reference_type' => 'order',
                'reference_id'   => $order->id,
                'description'    => "Refund za narudžbu #{$order->id}",
            ]);
        });
    }

    /**
     * Auto-release escrow for orders delivered 14+ days ago with no open dispute.
     * Called by scheduler: escrow:auto-release
     */
    public function autoRelease(): int
    {
        $releaseDays = config('escrow.auto_release_days', 14);

        $orders = \App\Models\Order::where('status', 'delivered')
            ->where('updated_at', '<=', now()->subDays($releaseDays))
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
