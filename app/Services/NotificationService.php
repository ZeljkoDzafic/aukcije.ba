<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\AuctionEndedNotification;
use App\Notifications\AuctionWonNotification;
use App\Notifications\DisputeNotification;
use App\Notifications\ItemShippedNotification;
use App\Notifications\KycStatusNotification;
use App\Notifications\OutbidNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentReminderNotification;
use App\Notifications\ShippingReminderNotification;

class NotificationService
{
    /**
     * Notify user when they've been outbid
     */
    public function sendOutbidNotification(User $user, Auction $auction, Bid $outbidBid, Bid $newLeadingBid): void
    {
        $user->notify(new OutbidNotification($auction, $outbidBid, $newLeadingBid));
    }

    /**
     * Notify winner when they win an auction
     */
    public function sendAuctionWonNotification(User $winner, Auction $auction, float $finalPrice): void
    {
        $winner->notify(new AuctionWonNotification($auction, $finalPrice));
    }

    /**
     * Notify seller when their auction ends
     */
    public function sendAuctionEndedNotification(User $seller, Auction $auction): void
    {
        $winnerName = $auction->winner?->name;
        $finalPrice = $auction->current_price;
        
        $seller->notify(new AuctionEndedNotification($auction, $winnerName, $finalPrice));
    }

    /**
     * Notify buyer when payment is received
     */
    public function sendPaymentReceivedNotification(User $buyer, Order $order, Payment $payment): void
    {
        $buyer->notify(new PaymentReceivedNotification($order, $payment));
    }

    /**
     * Notify buyer when item is shipped
     */
    public function sendItemShippedNotification(User $buyer, Order $order, Shipment $shipment): void
    {
        $buyer->notify(new ItemShippedNotification($order, $shipment));
    }

    /**
     * Notify parties about dispute updates
     */
    public function sendDisputeNotification(User $user, Dispute $dispute, string $type, ?string $message = null): void
    {
        $user->notify(new DisputeNotification($dispute, $type, $message));
    }

    /**
     * Notify user about KYC status changes
     */
    public function sendKycStatusNotification(User $user, string $status, ?string $message = null, ?int $kycLevel = null): void
    {
        $user->notify(new KycStatusNotification($status, $message, $kycLevel));
    }

    /**
     * Send payment reminder to buyer
     */
    public function sendPaymentReminder(User $buyer, Order $order, int $daysRemaining): void
    {
        $buyer->notify(new PaymentReminderNotification($order, $daysRemaining));
    }

    /**
     * Send shipping reminder to seller
     */
    public function sendShippingReminder(User $seller, Order $order, int $daysSincePayment): void
    {
        $seller->notify(new ShippingReminderNotification($order, $daysSincePayment));
    }

    /**
     * Broadcast real-time update to auction watchers
     */
    public function broadcastToAuctionWatchers(Auction $auction, string $event, array $data): void
    {
        // Implementation via Laravel Events/Broadcasting
        // This is handled by the event system
    }

    /**
     * Send bulk notifications to multiple users
     */
    public function sendBulkNotification(array $users, object $notification): void
    {
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    /**
     * Send notification via specific channel only
     */
    public function sendViaChannel(User $user, object $notification, string $channel): void
    {
        $user->notify($notification->via([$channel]));
    }
}
