<?php

/**
 * Auction Configuration
 * 
 * Core settings for the auction engine including:
 * - Anti-sniping rules
 * - Auction duration options
 * - Bid increment defaults
 * - State machine settings
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Anti-Sniping Configuration
    |--------------------------------------------------------------------------
    |
    | When a bid is placed within this window (in seconds) before the auction
    | ends, the auction will be automatically extended.
    |
    */

    'sniping_window' => env('AUCTION_SNIPING_WINDOW', 120), // 2 minutes

    /*
    |--------------------------------------------------------------------------
    | Auction Extension Time
    |--------------------------------------------------------------------------
    |
    | How many minutes to extend the auction when anti-sniping is triggered.
    |
    */

    'extension_minutes' => env('AUCTION_EXTENSION_MINUTES', 3),

    /*
    |--------------------------------------------------------------------------
    | Auto-Extension Enabled
    |--------------------------------------------------------------------------
    |
    | Whether anti-sniping (auto-extension) is enabled by default for new auctions.
    | Sellers can disable this when creating auctions.
    |
    */

    'auto_extension_enabled' => env('AUCTION_AUTO_EXTENSION', true),

    /*
    |--------------------------------------------------------------------------
    | Default Auction Duration
    |--------------------------------------------------------------------------
    |
    | Available duration options (in days) for auction creators.
    | First value is the default.
    |
    */

    'duration_options' => [3, 5, 7, 10],

    'default_duration' => env('AUCTION_DEFAULT_DURATION', 7),

    /*
    |--------------------------------------------------------------------------
    | Maximum Auction Duration
    |--------------------------------------------------------------------------
    |
    | Maximum number of days an auction can run.
    |
    */

    'max_duration' => env('AUCTION_MAX_DURATION', 30),

    /*
    |--------------------------------------------------------------------------
    | Bid Increment Rules
    |--------------------------------------------------------------------------
    |
    | Define minimum bid increments based on current price ranges.
    | Format: [max_price => increment]
    | The last rule applies to all prices above its max_price.
    |
    | Example: For current price 50 BAM, increment is 2.00 BAM
    |          For current price 150 BAM, increment is 5.00 BAM
    |
    */

    'bid_increments' => [
        10 => 0.50,    // 0 - 10 BAM: 0.50 increment
        50 => 1.00,    // 10 - 50 BAM: 1.00 increment
        100 => 2.00,   // 50 - 100 BAM: 2.00 increment
        500 => 5.00,   // 100 - 500 BAM: 5.00 increment
        1000 => 10.00, // 500 - 1000 BAM: 10.00 increment
        5000 => 25.00, // 1000 - 5000 BAM: 25.00 increment
        null => 50.00, // 5000+ BAM: 50.00 increment
    ],

    /*
    |--------------------------------------------------------------------------
    | Proxy Bidding Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the automatic proxy bidding system.
    |
    */

    'proxy_bidding_enabled' => env('FEATURE_PROXY_BIDDING', true),

    'max_proxy_bid' => env('AUCTION_MAX_PROXY_BID', 10000), // Maximum proxy bid amount

    /*
    |--------------------------------------------------------------------------
    | Buy Now Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for "Kupi Odmah" (Buy Now) feature.
    |
    */

    'buy_now_enabled' => true,

    'buy_now_max_discount' => 0.30, // Buy now price must be at least 30% above start price

    /*
    |--------------------------------------------------------------------------
    | Reserve Price Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for reserve (minimum) prices on auctions.
    |
    */

    'reserve_price_enabled' => true,

    'reserve_price_min_multiplier' => 1.5, // Reserve must be at least 1.5x start price

    /*
    |--------------------------------------------------------------------------
    | Auction State Transitions
    |--------------------------------------------------------------------------
    |
    | Define valid state transitions for the auction state machine.
    |
    */

    'state_transitions' => [
        'draft' => ['active', 'cancelled'],
        'active' => ['finished', 'sold', 'cancelled'],
        'finished' => ['sold'],
        'sold' => [], // Terminal state
        'cancelled' => [], // Terminal state
    ],

    /*
    |--------------------------------------------------------------------------
    | Auction Ending Schedule
    |--------------------------------------------------------------------------
    |
    | How often to check for expired auctions (in minutes).
    | This runs via Laravel Scheduler.
    |
    */

    'end_check_interval' => env('AUCTION_END_CHECK_INTERVAL', 1), // Every minute

    /*
    |--------------------------------------------------------------------------
    | Draft Auto-Cleanup
    |--------------------------------------------------------------------------
    |
    | Number of days after which unused drafts are automatically deleted.
    |
    */

    'draft_cleanup_days' => env('AUCTION_DRAFT_CLEANUP_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Featured Auction Settings
    |--------------------------------------------------------------------------
    |
    | Settings for featuring auctions on the homepage.
    |
    */

    'featured_limit' => env('AUCTION_FEATURED_LIMIT', 12), // Max featured auctions on homepage

    'featured_duration_hours' => env('AUCTION_FEATURED_DURATION', 24), // How long an auction stays featured

    /*
    |--------------------------------------------------------------------------
    | Auction Image Settings
    |--------------------------------------------------------------------------
    |
    | Image upload constraints for auction listings.
    |
    */

    'max_images' => env('AUCTION_MAX_IMAGES', 10),

    'max_image_size_mb' => env('AUCTION_MAX_IMAGE_SIZE_MB', 5),

    'allowed_image_types' => ['jpeg', 'png', 'webp'],

    'image_min_width' => 200,

    'image_min_height' => 200,

    'image_max_width' => 4096,

    'image_max_height' => 4096,

    /*
    |--------------------------------------------------------------------------
    | Redis Lock Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Redis-based concurrency locks during bidding.
    |
    */

    'lock_timeout_seconds' => env('AUCTION_LOCK_TIMEOUT', 5),

    'lock_wait_seconds' => env('AUCTION_LOCK_WAIT', 3),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | When to send notifications during auction lifecycle.
    |
    */

    'notify_on_outbid' => true,

    'notify_on_won' => true,

    'notify_on_ending_soon' => true,

    'ending_soon_threshold_minutes' => env('AUCTION_ENDING_SOON_MINUTES', 60), // Notify 1 hour before end

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Meilisearch configuration for auction search.
    |
    */

    'search_index' => env('MEILISEARCH_PREFIX', 'aukcije_') . 'auctions',

    'search_min_chars' => 2, // Minimum characters before search triggers

];
