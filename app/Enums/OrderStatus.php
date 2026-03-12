<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending         = 'pending';
    case PaymentReceived = 'payment_received';
    case Shipped         = 'shipped';
    case Delivered       = 'delivered';
    case Completed       = 'completed';
    case Disputed        = 'disputed';
    case Cancelled       = 'cancelled';
}
