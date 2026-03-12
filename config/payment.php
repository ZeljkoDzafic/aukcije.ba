<?php

declare(strict_types=1);

/**
 * Payment Configuration
 *
 * Settings for payment gateway integrations including:
 * - Gateway credentials
 * - Supported currencies per gateway
 * - Webhook secrets
 * - Payment flow settings
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | The default gateway for processing payments.
    | Options: 'stripe', 'monri', 'corvuspay', 'wallet'
    |
    */

    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Enabled Gateways
    |--------------------------------------------------------------------------
    |
    | Which payment gateways are enabled on the platform.
    | Users can choose from enabled gateways at checkout.
    |
    */

    'enabled' => [
        'stripe',
        'monri',
        'corvuspay',
        'wallet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway Display Order
    |--------------------------------------------------------------------------
    |
    | Order in which gateways appear on the payment selection screen.
    |
    */

    'display_order' => [
        'wallet',      // Internal wallet (recommended - instant)
        'stripe',      // International cards
        'monri',       // BiH local cards
        'corvuspay',   // Croatian cards
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Stripe payment gateway for international payments.
    | Supports EUR, USD, and other major currencies.
    |
    */

    'stripe' => [
        'enabled' => env('FEATURE_STRIPE', true),

        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

        // API Version (use Stripe's latest stable version)
        'api_version' => '2023-10-16',

        // Supported currencies
        'currencies' => ['EUR', 'USD', 'GBP'],

        // Default currency
        'currency' => env('STRIPE_CURRENCY', 'EUR'),

        // Payment method types
        'payment_methods' => [
            'card',
            'giropay',
            'sofort',
            'bancontact',
            'eps',
            'ideal',
        ],

        // Checkout settings
        'checkout' => [
            'success_url' => '/payment/success',
            'cancel_url' => '/payment/cancel',
            'locale' => 'bs', // Bosnian/Croatian/Serbian
            'allow_promotion_codes' => false,
            'billing_address' => 'required',
            'shipping_address' => 'required',
        ],

        // Webhook events to listen for
        'webhook_events' => [
            'checkout.session.completed',
            'payment_intent.succeeded',
            'payment_intent.payment_failed',
            'charge.refunded',
            'charge.dispute.created',
        ],

        // Statement descriptor (appears on customer's card statement)
        'statement_descriptor' => 'AUKCIJE.BA',

        // Capture settings
        'capture' => 'automatic', // 'automatic' or 'manual' (for escrow)

        // Retry settings for failed payments
        'retry_failed' => true,
        'max_retries' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monri Configuration
    |--------------------------------------------------------------------------
    |
    | Monri payment gateway for Bosnia and Herzegovina.
    | Supports BAM (Convertible Mark) and local cards.
    |
    | Documentation: https://monri.com/developers
    |
    */

    'monri' => [
        'enabled' => env('FEATURE_MONRI', true),

        'key' => env('MONRI_KEY'),
        'authenticity_token' => env('MONRI_AUTHENTICITY_TOKEN'),
        'webhook_secret' => env('MONRI_WEBHOOK_SECRET'),

        // API endpoints
        'api_url' => 'https://ipgtest.monri.com', // Test
        'api_url_production' => 'https://ipg.monri.com',

        // Supported currencies
        'currencies' => ['BAM', 'EUR'],

        // Default currency
        'currency' => env('MONRI_CURRENCY', 'BAM'),

        // Supported card types
        'card_types' => [
            'visa',
            'mastercard',
            'maestro',
            'amex',
        ],

        // Checkout settings
        'checkout' => [
            'success_url' => '/payment/success',
            'cancel_url' => '/payment/cancel',
            'language' => 'bs', // bs, hr, sr, en
            'display_mode' => 'popup', // popup, redirect, iframe
        ],

        // Transaction settings
        'transaction_type' => 'sale', // 'sale' or 'authorize' (for escrow)

        // Webhook events
        'webhook_events' => [
            'transaction_approved',
            'transaction_declined',
            'transaction_error',
        ],

        // Statement descriptor
        'statement_descriptor' => 'AUKCIJE.BA',

        // 3D Secure
        'three_d_secure' => 'optional', // required, optional, disabled
    ],

    /*
    |--------------------------------------------------------------------------
    | CorvusPay Configuration
    |--------------------------------------------------------------------------
    |
    | CorvusPay payment gateway for Croatia.
    | Supports EUR and Croatian bank cards.
    |
    | Documentation: https://corvuspay.com/docs
    |
    */

    'corvuspay' => [
        'enabled' => env('FEATURE_CORVUSPAY', true),

        'store_id' => env('CORVUSPAY_STORE_ID'),
        'secret_key' => env('CORVUSPAY_SECRET_KEY'),
        'webhook_secret' => env('CORVUSPAY_WEBHOOK_SECRET'),

        // API endpoints
        'api_url' => 'https://cardpay.corvuspay.com/authorize',

        // Supported currencies
        'currencies' => ['EUR', 'HRK'],

        // Default currency
        'currency' => env('CORVUSPAY_CURRENCY', 'EUR'),

        // Supported card types
        'card_types' => [
            'visa',
            'mastercard',
            'maestro',
            'diners',
        ],

        // Checkout settings
        'checkout' => [
            'success_url' => '/payment/success',
            'cancel_url' => '/payment/cancel',
            'language' => 'hr', // hr, en, de, it
            'display_mode' => 'redirect', // redirect, iframe, popup
        ],

        // Transaction settings
        'transaction_type' => 'sale',

        // Installment payments (common in Croatia)
        'installments' => [
            'enabled' => true,
            'max_installments' => 12,
        ],

        // Webhook events
        'webhook_events' => [
            'payment_success',
            'payment_failure',
            'payment_cancel',
        ],

        // 3D Secure
        'three_d_secure' => 'required',
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet Configuration
    |--------------------------------------------------------------------------
    |
    | Internal wallet payment system.
    | Instant payments from user's wallet balance.
    |
    */

    'wallet' => [
        'enabled' => env('FEATURE_WALLET', true),

        // Minimum balance required for wallet payment
        'min_balance' => 0,

        // Allow partial wallet payments (combine with other gateways)
        'allow_partial' => true,

        // Auto-top-up settings
        'auto_topup' => [
            'enabled' => false,
            'threshold' => 20, // Top up when balance below 20 BAM
            'amount' => 100,   // Top up by 100 BAM
            'gateway' => 'stripe', // Use this gateway for auto-topup
        ],

        // Transaction limits
        'limits' => [
            'min_payment' => 1,
            'max_payment' => 5000,
            'daily_limit' => 10000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Flow Settings
    |--------------------------------------------------------------------------
    |
    | General payment processing settings.
    |
    */

    // Payment deadline (how long to hold items after winning)
    'payment_deadline_days' => env('ESCROW_PAYMENT_DEADLINE', 3),

    // Auto-cancel unpaid orders after deadline
    'auto_cancel_unpaid' => true,

    // Send payment reminders
    'payment_reminders' => [
        'enabled' => true,
        'schedule' => [1, 2], // Send reminders on day 1 and 2 after winning
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for processing refunds.
    |
    */

    'refund' => [
        // Allow partial refunds
        'partial_refunds' => true,

        // Refund to original payment method only
        'original_method_only' => true,

        // Refund processing time (business days)
        'processing_days' => '5-10',

        // Require reason for refund
        'require_reason' => true,

        // Valid refund reasons
        'valid_reasons' => [
            'item_not_received',
            'not_as_described',
            'damaged',
            'counterfeit',
            'wrong_item',
            'buyer_changed_mind',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Formatting
    |--------------------------------------------------------------------------
    |
    | How to display prices and amounts.
    |
    */

    'formatting' => [
        'BAM' => [
            'symbol' => 'KM',
            'position' => 'after', // before or after
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ],
        'EUR' => [
            'symbol' => '€',
            'position' => 'after',
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ],
        'USD' => [
            'symbol' => '$',
            'position' => 'before',
            'decimal_places' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
        'HRK' => [
            'symbol' => 'kn',
            'position' => 'after',
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ],
        'RSD' => [
            'symbol' => 'RSD',
            'position' => 'after',
            'decimal_places' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Debugging
    |--------------------------------------------------------------------------
    |
    | Payment logging settings.
    |
    */

    'logging' => [
        'enabled' => true,
        'log_all_requests' => true, // Log all API requests
        'log_responses' => true,    // Log all API responses
        'mask_sensitive' => true,   // Mask card numbers, CVV, etc.
        'retention_days' => 365,    // Keep logs for 1 year
    ],

    /*
    |--------------------------------------------------------------------------
    | Fraud Prevention
    |--------------------------------------------------------------------------
    |
    | Anti-fraud settings for payments.
    |
    */

    'fraud' => [
        // Enable Stripe Radar
        'stripe_radar' => true,

        // Enable Monri fraud detection
        'monri_fraud_detection' => true,

        // Custom fraud rules
        'rules' => [
            'max_amount_per_transaction' => 5000,
            'max_transactions_per_hour' => 5,
            'require_cvv' => true,
            'require_avs' => false, // Address verification (not common in Balkans)
        ],

        // Block high-risk countries (optional)
        'blocked_countries' => [
            // Add ISO country codes if needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for handling payment webhooks.
    |
    */

    'webhooks' => [
        // Path for webhook endpoints
        'path' => '/api/webhooks/payments',

        // Signature verification
        'verify_signature' => true,

        // Retry failed webhooks
        'retry_failed' => true,
        'max_retries' => 5,
        'retry_interval_minutes' => 5,

        // Timeout for webhook processing
        'timeout_seconds' => 30,
    ],

];
