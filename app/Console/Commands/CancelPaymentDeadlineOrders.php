<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\PaymentDeadlineCancelJob;
use App\Services\EscrowService;
use Illuminate\Console\Command;

class CancelPaymentDeadlineOrders extends Command
{
    protected $signature   = 'orders:cancel-payment-deadline';
    protected $description = 'Cancel orders where buyers missed the payment deadline (T-1306)';

    public function handle(EscrowService $escrowService): int
    {
        $this->info('Cancelling overdue orders...');

        PaymentDeadlineCancelJob::dispatchSync($escrowService);

        $this->info('Done.');

        return self::SUCCESS;
    }
}
