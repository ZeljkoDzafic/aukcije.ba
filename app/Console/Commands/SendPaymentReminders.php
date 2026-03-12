<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'orders:payment-reminders';

    protected $description = 'Send payment reminder notifications to buyers with unpaid orders';

    public function handle(): int
    {
        $deadline = config('escrow.payment_deadline_days', 3);

        $orders = Order::query()
            ->with(['buyer', 'auction'])
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '>', now())
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            if (! $order->buyer) {
                continue;
            }

            $daysRemaining = (int) now()->diffInDays($order->payment_deadline_at, false);

            if ($daysRemaining <= 0) {
                continue;
            }

            // Send on day 1 remaining or day 2 remaining only
            if (in_array($daysRemaining, [1, 2], true)) {
                $order->buyer->notify(new PaymentReminderNotification($order, $daysRemaining));
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} payment reminder(s).");
        }

        return self::SUCCESS;
    }
}
