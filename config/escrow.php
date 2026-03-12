<?php

declare(strict_types=1);

/**
 * Escrow Configuration
 *
 * Settings for the escrow/payment protection system including:
 * - Payment deadlines
 * - Auto-release timelines
 * - Commission rates
 * - Refund policies
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Escrow Enabled
    |--------------------------------------------------------------------------
    |
    | Whether the escrow system is enabled platform-wide.
    |
    */

    'enabled' => env('FEATURE_ESCROW', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Deadline
    |--------------------------------------------------------------------------
    |
    | How many days the buyer has to pay after winning an auction.
    | After this period, the order can be cancelled.
    |
    */

    'payment_deadline_days' => env('ESCROW_PAYMENT_DEADLINE', 3),

    /*
    |--------------------------------------------------------------------------
    | Auto-Release Period
    |--------------------------------------------------------------------------
    |
    | How many days after delivery confirmation (or auto-confirmation)
    | the funds are automatically released to the seller.
    |
    */

    'auto_release_days' => env('ESCROW_AUTO_RELEASE', 14),

    /*
    |--------------------------------------------------------------------------
    | Shipping Deadline
    |--------------------------------------------------------------------------
    |
    | How many days the seller has to ship the item after payment.
    |
    */

    'shipping_deadline_days' => env('ESCROW_SHIPPING_DEADLINE', 5),

    /*
    |--------------------------------------------------------------------------
    | Delivery Auto-Confirmation
    |--------------------------------------------------------------------------
    |
    | If buyer doesn't confirm delivery, the system auto-confirms
    | after this many days from shipping.
    |
    */

    'delivery_auto_confirm_days' => env('ESCROW_DELIVERY_AUTO_CONFIRM', 21),

    /*
    |--------------------------------------------------------------------------
    | Commission Structure
    |--------------------------------------------------------------------------
    |
    | Platform commission rates based on seller tier.
    | These are percentages (e.g., 8 = 8%).
    |
    */

    'commission' => [
        'free' => env('COMMISSION_FREE', 8),        // 8% for Free tier
        'premium' => env('COMMISSION_PREMIUM', 5),  // 5% for Premium tier
        'storefront' => env('COMMISSION_STOREFRONT', 3), // 3% for Storefront tier
    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Commission
    |--------------------------------------------------------------------------
    |
    | Minimum commission amount in BAM (regardless of percentage).
    | If calculated commission is less than this, use this value.
    |
    */

    'minimum_commission' => env('ESCROW_MIN_COMMISSION', 1.00),

    /*
    |--------------------------------------------------------------------------
    | Maximum Commission
    |--------------------------------------------------------------------------
    |
    | Maximum commission cap in BAM.
    | If calculated commission exceeds this, cap at this value.
    | Set to null for no cap.
    |
    */

    'maximum_commission' => env('ESCROW_MAX_COMMISSION', null),

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Settings
    |--------------------------------------------------------------------------
    |
    | Rules for seller withdrawals from wallet.
    |
    */

    'withdrawal' => [
        'min_amount' => env('WITHDRAWAL_MIN_AMOUNT', 20), // Minimum withdrawal in BAM
        'max_amount' => env('WITHDRAWAL_MAX_AMOUNT', 5000), // Maximum per transaction
        'daily_limit' => env('WITHDRAWAL_DAILY_LIMIT', 10000), // Daily limit per seller
        'processing_days' => env('WITHDRAWAL_PROCESSING', '1-3'), // Business days for processing
        'fee_percentage' => env('WITHDRAWAL_FEE', 0), // Withdrawal fee percentage (0 = free)
    ],

    /*
    |--------------------------------------------------------------------------
    | Deposit Settings
    |--------------------------------------------------------------------------
    |
    | Rules for buyer deposits to wallet.
    |
    */

    'deposit' => [
        'min_amount' => env('DEPOSIT_MIN_AMOUNT', 10), // Minimum deposit in BAM
        'max_amount' => env('DEPOSIT_MAX_AMOUNT', 10000), // Maximum per transaction
        'daily_limit' => env('DEPOSIT_DAILY_LIMIT', 20000), // Daily limit per user
    ],

    /*
    |--------------------------------------------------------------------------
    | Dispute Settings
    |--------------------------------------------------------------------------
    |
    | Rules for disputes and escrow freezes.
    |
    */

    'dispute' => [
        'auto_freeze' => true, // Automatically freeze escrow when dispute opened
        'seller_response_hours' => env('DISPUTE_SELLER_RESPONSE', 48), // Seller has 48h to respond
        'admin_resolution_days' => env('DISPUTE_RESOLUTION', 5), // Admin has 5 days to resolve
        'escalation_auto' => true, // Auto-escalate if seller doesn't respond
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Policy
    |--------------------------------------------------------------------------
    |
    | Rules for partial and full refunds.
    |
    */

    'refund' => [
        'full_refund_reasons' => [
            'item_not_received',
            'significantly_not_as_described',
            'counterfeit',
            'damaged_in_transit',
            'seller_cancelled',
        ],
        'partial_refund_max_percentage' => env('REFUND_MAX_PARTIAL', 50), // Max 50% partial refund
        'return_shipping_buyer_pays' => false, // Who pays return shipping for valid disputes
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Primary and supported currencies.
    |
    */

    'primary_currency' => env('PRIMARY_CURRENCY', 'BAM'),

    'supported_currencies' => ['BAM', 'EUR', 'USD', 'HRK', 'RSD'],

    'exchange_rate_provider' => env('EXCHANGE_RATE_PROVIDER', 'exchangerate.host'),

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    |
    | Valid transaction types for wallet_transactions table.
    |
    */

    'transaction_types' => [
        'deposit',           // Money added to wallet
        'withdrawal',        // Money withdrawn from wallet
        'bid_hold',         // Money held for bid (proxy bids)
        'bid_release',      // Hold released (outbid or auction ended without win)
        'escrow_hold',      // Money held in escrow (buyer paid)
        'escrow_release',   // Escrow released to seller
        'commission',       // Platform commission deducted
        'refund',           // Refund to buyer
        'payment',          // Direct payment
        'adjustment',       // Manual admin adjustment
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet Balance Alerts
    |--------------------------------------------------------------------------
    |
    | Thresholds for low balance notifications.
    |
    */

    'low_balance_threshold' => env('WALLET_LOW_BALANCE_THRESHOLD', 50),

    'low_balance_notification' => true,

    /*
    |--------------------------------------------------------------------------
    | Anti-Fraud Settings
    |--------------------------------------------------------------------------
    |
    | Rules to prevent fraud and money laundering.
    |
    */

    'fraud' => [
        'new_user_deposit_limit' => env('FRAUD_NEW_USER_DEPOSIT', 500), // Max deposit for accounts < 7 days
        'new_user_withdrawal_limit' => env('FRAUD_NEW_USER_WITHDRAW', 100), // Max withdrawal for accounts < 7 days
        'velocity_check_window_hours' => 24, // Time window for velocity checks
        'velocity_check_max_transactions' => 10, // Max transactions in window before flag
        'kyc_required_for_withdrawal' => env('KYC_WITHDRAWAL_THRESHOLD', 1000), // KYC required for withdrawals above this
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduled Jobs
    |--------------------------------------------------------------------------
    |
    | Timing for automated escrow jobs.
    |
    */

    'schedule' => [
        'auto_release_frequency' => 'hourly', // How often to check for auto-releases
        'payment_reminder_frequency' => 'every6hours', // Payment reminder checks
        'shipping_reminder_frequency' => 'every6hours', // Shipping reminder checks
    ],

];
