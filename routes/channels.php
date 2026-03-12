<?php

use App\Models\Auction;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels (WebSocket)
|--------------------------------------------------------------------------
|
| Define all broadcast channels for real-time functionality.
| Powered by Laravel Reverb.
|
*/

/*
|--------------------------------------------------------------------------
| Public Channels
|--------------------------------------------------------------------------
*/

// Auction channel - anyone can listen to auction updates
Broadcast::channel('auction.{auctionId}', function ($user, $auctionId) {
    // Public channel - anyone can listen
    return true;
});

// Global channel for site-wide announcements
Broadcast::channel('aukcije.global', function ($user) {
    // Public channel
    return true;
});

/*
|--------------------------------------------------------------------------
| Private Channels (Authentication Required)
|--------------------------------------------------------------------------
*/

// User's private channel for personal notifications
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return $user->id === (int) $userId;
});

// Order participants channel
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = \App\Models\Order::find($orderId);
    
    if (!$order) {
        return false;
    }
    
    // Buyer or seller can listen to order updates
    return $user->id === $order->buyer_id || $user->id === $order->seller_id;
});

// Dispute channel
Broadcast::channel('dispute.{disputeId}', function ($user, $disputeId) {
    $dispute = \App\Models\Dispute::find($disputeId);
    
    if (!$dispute) {
        return false;
    }
    
    // Parties involved in dispute + moderators/admins
    return $user->id === $dispute->order->buyer_id 
        || $user->id === $dispute->order->seller_id
        || $user->hasRole(['admin', 'moderator']);
});

/*
|--------------------------------------------------------------------------
| Presence Channels (Authentication + User List)
|--------------------------------------------------------------------------
*/

// Active bidders on an auction
Broadcast::channel('auction.{auctionId}.bidders', function ($user, $auctionId) {
    $auction = Auction::find($auctionId);
    
    if (!$auction || !$auction->isActive()) {
        return false;
    }
    
    // Return user data for presence channel
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->profile?->avatar_url,
        'trust_score' => $user->trust_score,
    ];
}, ['guards' => ['web', 'sanctum']]);

// Category watchers
Broadcast::channel('category.{categoryId}.watchers', function ($user, $categoryId) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
}, ['guards' => ['web', 'sanctum']]);

/*
|--------------------------------------------------------------------------
| Admin Channels
|--------------------------------------------------------------------------
*/

// Admin notifications channel
Broadcast::channel('admin.notifications', function ($user) {
    return $user->hasRole(['admin', 'moderator']);
}, ['guards' => ['web', 'sanctum']]);

// Live moderation channel
Broadcast::channel('admin.moderation', function ($user) {
    return $user->hasRole(['admin', 'moderator']);
}, ['guards' => ['web', 'sanctum']]);
