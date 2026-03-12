<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Services\EscrowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * T-1306: Auto-cancel orders where buyer missed the payment deadline.
 * Dispatched by `orders:cancel-payment-deadline` scheduled command (hourly).
 */
class PaymentDeadlineCancelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(private readonly EscrowService $escrowService) {}

    public function handle(): void
    {
        $cancelled = 0;

        Order::query()
            ->where('status', 'pending_payment')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '<', now())
            ->chunk(50, function ($orders) use (&$cancelled) {
                foreach ($orders as $order) {
                    try {
                        DB::transaction(function () use ($order) {
                            $order->update([
                                'status'       => 'cancelled',
                                'cancelled_at' => now(),
                            ]);

                            // Release any held escrow funds
                            if ($order->payment_status === 'escrow_held') {
                                $this->escrowService->refundBuyer($order, (float) $order->total_amount);
                            }
                        });

                        $cancelled++;
                    } catch (\Throwable $e) {
                        Log::warning("PaymentDeadlineCancelJob: failed to cancel order {$order->id}: {$e->getMessage()}");
                    }
                }
            });

        Log::info("PaymentDeadlineCancelJob: cancelled {$cancelled} orders.");
    }
}
