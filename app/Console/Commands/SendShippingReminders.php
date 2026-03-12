<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\ShippingReminderNotification;
use Illuminate\Console\Command;

class SendShippingReminders extends Command
{
    protected $signature = 'orders:shipping-reminders';

    protected $description = 'Send shipping reminder notifications to sellers who have not shipped paid orders';

    public function handle(): int
    {
        $orders = Order::query()
            ->with(['seller', 'auction', 'buyer'])
            ->where('payment_status', 'paid')
            ->where('status', 'awaiting_shipment')
            ->whereNotNull('paid_at')
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            if (! $order->seller || ! $order->paid_at) {
                continue;
            }

            $daysSincePayment = (int) $order->paid_at->diffInDays(now());

            // Remind after 2 days and again after 4 days
            if (in_array($daysSincePayment, [2, 4], true)) {
                $order->seller->notify(new ShippingReminderNotification($order, $daysSincePayment));
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} shipping reminder(s).");
        }

        return self::SUCCESS;
    }
}
