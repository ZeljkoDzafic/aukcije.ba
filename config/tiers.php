<?php

/**
 * Seller Tier Configuration
 * 
 * Defines seller subscription tiers, limits, and benefits.
 * 
 * Tiers:
 * - Free: Basic tier with limited auctions
 * - Premium: Mid-tier with more auctions and lower commission
 * - Storefront: Unlimited auctions, lowest commission, custom features
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Tier Definitions
    |--------------------------------------------------------------------------
    |
    | Each tier has:
    | - name: Display name
    | - slug: Database/internal identifier
    | - price: Monthly subscription price in BAM
    | - auction_limit: Max active auctions at once
    | - commission: Platform commission percentage
    | - features: Array of enabled features
    |
    */

    'tiers' => [
        'free' => [
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'currency' => 'BAM',
            'billing_period' => 'monthly',
            
            // Limits
            'auction_limit' => 5,
            'auction_duration_max' => 7, // Max days per auction
            'images_per_auction' => 5,
            'featured_auctions' => 0, // Can't feature auctions
            'bulk_upload' => false,
            
            // Commission
            'commission_rate' => 8, // 8%
            
            // Features
            'features' => [
                'basic_analytics' => true,
                'email_support' => true,
                'mobile_app' => true,
                'escrow_protection' => true,
                'rating_system' => true,
                'messaging' => true,
                'api_access' => false,
                'priority_support' => false,
                'custom_branding' => false,
                'advanced_analytics' => false,
                'dedicated_account_manager' => false,
            ],
            
            // Visibility
            'badge' => null,
            'badge_color' => 'gray',
        ],

        'premium' => [
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 29,
            'currency' => 'BAM',
            'billing_period' => 'monthly',
            
            // Limits
            'auction_limit' => 50,
            'auction_duration_max' => 14,
            'images_per_auction' => 10,
            'featured_auctions' => 3, // Can feature up to 3 auctions at once
            'bulk_upload' => true,
            
            // Commission
            'commission_rate' => 5, // 5%
            
            // Features
            'features' => [
                'basic_analytics' => true,
                'email_support' => true,
                'mobile_app' => true,
                'escrow_protection' => true,
                'rating_system' => true,
                'messaging' => true,
                'api_access' => true,
                'priority_support' => true,
                'custom_branding' => false,
                'advanced_analytics' => true,
                'dedicated_account_manager' => false,
            ],
            
            // Visibility
            'badge' => 'Premium',
            'badge_color' => 'blue',
        ],

        'storefront' => [
            'name' => 'Storefront',
            'slug' => 'storefront',
            'price' => 99,
            'currency' => 'BAM',
            'billing_period' => 'monthly',
            
            // Limits
            'auction_limit' => -1, // Unlimited (-1 = no limit)
            'auction_duration_max' => 30,
            'images_per_auction' => 20,
            'featured_auctions' => -1, // Unlimited featured
            'bulk_upload' => true,
            
            // Commission
            'commission_rate' => 3, // 3%
            
            // Features
            'features' => [
                'basic_analytics' => true,
                'email_support' => true,
                'mobile_app' => true,
                'escrow_protection' => true,
                'rating_system' => true,
                'messaging' => true,
                'api_access' => true,
                'priority_support' => true,
                'custom_branding' => true,
                'advanced_analytics' => true,
                'dedicated_account_manager' => true,
            ],
            
            // Visibility
            'badge' => 'Verified Store',
            'badge_color' => 'gold',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tier Upgrade/Downgrade Rules
    |--------------------------------------------------------------------------
    |
    | Settings for subscription changes.
    |
    */

    'upgrade' => [
        'immediate' => true, // Upgrades take effect immediately
        'prorate' => false, // Whether to prorate the price difference
    ],

    'downgrade' => [
        'immediate' => false, // Downgrades take effect at end of billing period
        'allow_if_over_limit' => false, // Can't downgrade if over new tier's limits
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Period
    |--------------------------------------------------------------------------
    |
    | Free trial settings for new sellers.
    |
    */

    'trial' => [
        'enabled' => true,
        'duration_days' => 14, // 14-day free trial
        'tier' => 'premium', // Trial gives Premium features
        'credit_card_required' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Cancellation
    |--------------------------------------------------------------------------
    |
    | Rules for subscription cancellation.
    |
    */

    'cancellation' => [
        'immediate' => false, // Access continues until end of billing period
        'refund_policy' => 'none', // none, prorated, full
        'grace_period_days' => 30, // Keep data for 30 days after cancellation
        'reactivation_window_days' => 90, // Can reactivate within 90 days with same tier
    ],

    /*
    |--------------------------------------------------------------------------
    | Auction Limits Enforcement
    |--------------------------------------------------------------------------
    |
    | How strictly to enforce auction limits.
    |
    */

    'enforce_auction_limit' => true, // Block creation if over limit
    'soft_limit_warning' => true, // Show warning when approaching limit
    'warning_threshold' => 0.8, // Warn at 80% of limit

    /*
    |--------------------------------------------------------------------------
    | Commission Settings
    |--------------------------------------------------------------------------
    |
    | How commission is calculated and applied.
    |
    */

    'commission' => [
        'apply_on' => 'final_price', // 'start_price' or 'final_price'
        'include_shipping' => false, // Don't charge commission on shipping
        'minimum_amount' => 1.00, // Minimum commission in BAM
        'maximum_amount' => null, // No cap by default
        'tax_rate' => 0.17, // 17% VAT on commission (BiH rate)
    ],

    /*
    |--------------------------------------------------------------------------
    | Featured Auction Pricing
    |--------------------------------------------------------------------------
    |
    | Cost to feature an auction (for tiers that allow it).
    | Priced per day.
    |
    */

    'featured_pricing' => [
        'price_per_day' => 5, // 5 BAM per day
        'min_days' => 1,
        'max_days' => 7,
        'discount_3days' => 0.10, // 10% off for 3+ days
        'discount_7days' => 0.20, // 20% off for 7 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Tier Benefits Summary (for display)
    |--------------------------------------------------------------------------
    |
    | Human-readable benefit descriptions for pricing page.
    |
    */

    'benefits' => [
        'free' => [
            '5 active auctions',
            'Basic analytics',
            'Escrow protection',
            'Email support',
            '8% commission',
        ],
        'premium' => [
            '50 active auctions',
            'Advanced analytics',
            'API access',
            'Priority support',
            '3 featured auctions',
            '5% commission',
            'Bulk upload',
        ],
        'storefront' => [
            'Unlimited auctions',
            'Custom branding',
            'Dedicated account manager',
            'Unlimited featured auctions',
            '3% commission',
            'Maximum visibility',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | When to notify sellers about their subscription.
    |
    */

    'notifications' => [
        'trial_ending_days' => [3, 1], // Notify 3 and 1 days before trial ends
        'subscription_ending_days' => [7, 3, 1], // Notify before subscription expires
        'payment_failed' => true,
        'tier_limit_approaching' => true, // Warn when approaching auction limit
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Tier Migration
    |--------------------------------------------------------------------------
    |
    | If you had old tiers before this structure, map them here.
    |
    */

    'legacy_mapping' => [
        // 'old_tier_slug' => 'new_tier_slug',
    ],

];
