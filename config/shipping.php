<?php

declare(strict_types=1);

/**
 * Shipping Configuration
 *
 * Settings for courier integrations including:
 * - Courier API credentials
 * - Shipping zones and pricing
 * - Waybill generation
 * - Tracking webhooks
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Courier
    |--------------------------------------------------------------------------
    |
    | The default courier for shipping calculations.
    |
    */

    'default' => env('SHIPPING_DEFAULT_COURIER', 'euroexpress'),

    /*
    |--------------------------------------------------------------------------
    | Enabled Couriers
    |--------------------------------------------------------------------------
    |
    | Which couriers are enabled on the platform.
    |
    */

    'enabled' => [
        'euroexpress',
        'postexpress',
        'bhposta',
    ],

    /*
    |--------------------------------------------------------------------------
    | EuroExpress Configuration
    |--------------------------------------------------------------------------
    |
    | EuroExpress courier for Bosnia and Herzegovina.
    |
    */

    'euroexpress' => [
        'enabled' => env('FEATURE_EUROEXPRESS', true),

        'api_key' => env('EUROEXPRESS_API_KEY'),
        'api_secret' => env('EUROEXPRESS_API_SECRET'),

        // API endpoints
        'api_url' => 'https://api.euroexpress.ba/v1',
        'sandbox_url' => 'https://sandbox-api.euroexpress.ba/v1',
        'sandbox' => env('EUROEXPRESS_SANDBOX', true),

        // Default sender info
        'sender' => [
            'name' => env('EUROEXPRESS_SENDER_NAME', 'Aukcije.ba'),
            'company' => env('EUROEXPRESS_SENDER_COMPANY', 'Aukcije d.o.o.'),
            'address' => env('EUROEXPRESS_SENDER_ADDRESS', 'Zmaja od Bosne 1'),
            'city' => env('EUROEXPRESS_SENDER_CITY', 'Sarajevo'),
            'postal_code' => env('EUROEXPRESS_SENDER_POSTAL', '71000'),
            'country' => 'BA',
            'phone' => env('EUROEXPRESS_SENDER_PHONE', '+387 33 000 000'),
            'email' => env('EUROEXPRESS_SENDER_EMAIL', 'info@mojeaukcije.com'),
        ],

        // Services offered
        'services' => [
            'standard' => [
                'name' => 'EuroExpress Standard',
                'delivery_days' => '1-2',
                'tracking' => true,
                'insurance_included' => true,
                'max_weight_kg' => 30,
            ],
            'express' => [
                'name' => 'EuroExpress Express',
                'delivery_days' => '1',
                'tracking' => true,
                'insurance_included' => true,
                'max_weight_kg' => 30,
                'price_multiplier' => 1.5,
            ],
        ],

        // Pricing (base rates in BAM)
        'pricing' => [
            'base_price' => 5.00,
            'per_kg' => 1.00,
            'free_shipping_threshold' => 100, // Free shipping for orders over 100 BAM
            'insurance_percentage' => 0.02, // 2% of declared value
            'cod_percentage' => 0.015, // 1.5% for cash on delivery
        ],

        // Waybill settings
        'waybill' => [
            'format' => 'A6', // A6 or A4
            'print_copies' => 2, // Number of copies to print
            'auto_generate' => true, // Generate waybill when order marked as shipped
        ],

        // Tracking webhook
        'webhook_secret' => env('EUROEXPRESS_WEBHOOK_SECRET'),
        'tracking_events' => [
            'picked_up',
            'in_transit',
            'out_for_delivery',
            'delivered',
            'delivery_failed',
            'returned',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | PostExpress Configuration
    |--------------------------------------------------------------------------
    |
    | PostExpress (Hrvatska pošta) for Croatia.
    |
    */

    'postexpress' => [
        'enabled' => env('FEATURE_POSTEXPRESS', true),

        'api_key' => env('POSTEXPRESS_API_KEY'),
        'api_secret' => env('POSTEXPRESS_API_SECRET'),

        // API endpoints
        'api_url' => 'https://api.postexpress.hr/v2',
        'sandbox_url' => 'https://sandbox-api.postexpress.hr/v2',
        'sandbox' => env('POSTEXPRESS_SANDBOX', true),

        // Default sender info
        'sender' => [
            'name' => env('POSTEXPRESS_SENDER_NAME', 'Aukcije.ba'),
            'company' => env('POSTEXPRESS_SENDER_COMPANY', 'Aukcije d.o.o.'),
            'address' => env('POSTEXPRESS_SENDER_ADDRESS', 'Zmaja od Bosne 1'),
            'city' => env('POSTEXPRESS_SENDER_CITY', 'Sarajevo'),
            'postal_code' => env('POSTEXPRESS_SENDER_POSTAL', '71000'),
            'country' => 'BA',
            'phone' => env('POSTEXPRESS_SENDER_PHONE', '+387 33 000 000'),
            'email' => env('POSTEXPRESS_SENDER_EMAIL', 'info@mojeaukcije.com'),
            'oib' => env('POSTEXPRESS_SENDER_OIB', ''), // Croatian tax ID
        ],

        // Services offered
        'services' => [
            'standard' => [
                'name' => 'PostExpress Standard',
                'delivery_days' => '2-3',
                'tracking' => true,
                'insurance_included' => true,
                'max_weight_kg' => 20,
            ],
            'priority' => [
                'name' => 'PostExpress Priority',
                'delivery_days' => '1-2',
                'tracking' => true,
                'insurance_included' => true,
                'max_weight_kg' => 20,
                'price_multiplier' => 1.4,
            ],
        ],

        // Pricing (base rates in EUR)
        'pricing' => [
            'base_price' => 3.50,
            'per_kg' => 0.80,
            'free_shipping_threshold' => 70,
            'insurance_percentage' => 0.015,
            'cod_percentage' => 0.012,
        ],

        // Waybill settings
        'waybill' => [
            'format' => 'A6',
            'print_copies' => 2,
            'auto_generate' => true,
        ],

        // Tracking webhook
        'webhook_secret' => env('POSTEXPRESS_WEBHOOK_SECRET'),
        'tracking_events' => [
            'accepted',
            'in_transit',
            'arrived_at_destination',
            'out_for_delivery',
            'delivered',
            'delivery_failed',
            'returned',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | BH Pošta Configuration
    |--------------------------------------------------------------------------
    |
    | BH Pošta for domestic shipping in Bosnia.
    |
    */

    'bhposta' => [
        'enabled' => env('FEATURE_BHPOSTA', true),

        'api_key' => env('BHPOSTA_API_KEY'),
        'api_secret' => env('BHPOSTA_API_SECRET'),

        // API endpoints
        'api_url' => 'https://api.bhposta.ba/v1',
        'sandbox_url' => 'https://sandbox-api.bhposta.ba/v1',
        'sandbox' => env('BHPOSTA_SANDBOX', true),

        // Default sender info
        'sender' => [
            'name' => env('BHPOSTA_SENDER_NAME', 'Aukcije.ba'),
            'company' => env('BHPOSTA_SENDER_COMPANY', 'Aukcije d.o.o.'),
            'address' => env('BHPOSTA_SENDER_ADDRESS', 'Zmaja od Bosne 1'),
            'city' => env('BHPOSTA_SENDER_CITY', 'Sarajevo'),
            'postal_code' => env('BHPOSTA_SENDER_POSTAL', '71000'),
            'country' => 'BA',
            'phone' => env('BHPOSTA_SENDER_PHONE', '+387 33 000 000'),
            'email' => env('BHPOSTA_SENDER_EMAIL', 'info@mojeaukcije.com'),
        ],

        // Services offered
        'services' => [
            'standard' => [
                'name' => 'BH Pošta Standard',
                'delivery_days' => '2-4',
                'tracking' => true,
                'insurance_included' => false,
                'max_weight_kg' => 20,
            ],
            'express' => [
                'name' => 'BH Pošta Express',
                'delivery_days' => '1-2',
                'tracking' => true,
                'insurance_included' => true,
                'max_weight_kg' => 20,
                'price_multiplier' => 1.6,
            ],
        ],

        // Pricing (base rates in BAM)
        'pricing' => [
            'base_price' => 4.00,
            'per_kg' => 0.90,
            'free_shipping_threshold' => 80,
            'insurance_percentage' => 0.02,
            'cod_percentage' => 0.015,
        ],

        // Waybill settings
        'waybill' => [
            'format' => 'A6',
            'print_copies' => 2,
            'auto_generate' => true,
        ],

        // Tracking webhook
        'webhook_secret' => env('BHPOSTA_WEBHOOK_SECRET'),
        'tracking_events' => [
            'accepted',
            'in_transit',
            'arrived_at_destination',
            'out_for_delivery',
            'delivered',
            'delivery_failed',
            'returned',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Zones
    |--------------------------------------------------------------------------
    |
    | Define shipping zones for rate calculations.
    |
    */

    'zones' => [
        'domestic_biH' => [
            'name' => 'Bosna i Hercegovina',
            'countries' => ['BA'],
            'couriers' => ['euroexpress', 'bhposta'],
            'default_courier' => 'euroexpress',
        ],
        'croatia' => [
            'name' => 'Hrvatska',
            'countries' => ['HR'],
            'couriers' => ['postexpress'],
            'default_courier' => 'postexpress',
        ],
        'serbia' => [
            'name' => 'Srbija',
            'countries' => ['RS'],
            'couriers' => ['euroexpress'],
            'default_courier' => 'euroexpress',
        ],
        'slovenia' => [
            'name' => 'Slovenija',
            'countries' => ['SI'],
            'couriers' => ['postexpress'],
            'default_courier' => 'postexpress',
        ],
        'regional' => [
            'name' => 'Regional (Ex-Yu)',
            'countries' => ['BA', 'HR', 'RS', 'SI', 'ME', 'MK'],
            'couriers' => ['euroexpress', 'postexpress', 'bhposta'],
            'default_courier' => 'euroexpress',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Settings
    |--------------------------------------------------------------------------
    |
    | Default package dimensions and weight limits.
    |
    */

    'package' => [
        'default_dimensions' => [
            'length_cm' => 40,
            'width_cm' => 30,
            'height_cm' => 20,
        ],
        'max_dimensions' => [
            'length_cm' => 150,
            'width_cm' => 100,
            'height_cm' => 100,
            'max_weight_kg' => 30,
        ],
        'volumetric_divisor' => 5000, // For volumetric weight calculation
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Rules
    |--------------------------------------------------------------------------
    |
    | General shipping configuration.
    |
    */

    // Require shipping address for all orders
    'require_address' => true,

    // Allow pickup option
    'allow_pickup' => true,

    // Auto-select best courier (cheapest)
    'auto_select_courier' => true,

    // Allow buyer to choose courier
    'buyer_can_choose' => true,

    // Shipping cost paid by
    'default_payer' => 'buyer', // 'buyer' or 'seller'

    /*
    |--------------------------------------------------------------------------
    | Tracking Settings
    |--------------------------------------------------------------------------
    |
    | How tracking updates are handled.
    |
    */

    'tracking' => [
        // Poll couriers for updates (if webhooks not available)
        'poll_interval_hours' => 6,

        // Notify buyer on tracking updates
        'notify_on_update' => true,

        // Tracking page URL format
        'tracking_url_template' => 'https://track.{courier}.ba/{tracking_number}',

        // Store tracking history
        'store_history' => true,
        'history_retention_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Waybill Generation
    |--------------------------------------------------------------------------
    |
    | Settings for waybill (tovarni list) generation.
    |
    */

    'waybill' => [
        // PDF generation settings
        'paper_size' => 'A6',
        'orientation' => 'landscape',

        // Include QR code
        'include_qr_code' => true,

        // Include barcode
        'include_barcode' => true,

        // Auto-generate on order ship
        'auto_generate' => true,

        // Email waybill to buyer
        'email_to_buyer' => true,

        // Email waybill to seller
        'email_to_seller' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Insurance Settings
    |--------------------------------------------------------------------------
    |
    | Shipping insurance configuration.
    |
    */

    'insurance' => [
        // Include insurance by default
        'default_enabled' => true,

        // Maximum insured value
        'max_value' => 5000,

        // Insurance rate (percentage of declared value)
        'rate' => 0.02, // 2%

        // Minimum insurance cost
        'minimum_cost' => 1.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cash on Delivery (COD)
    |--------------------------------------------------------------------------
    |
    | COD settings for payments on delivery.
    |
    */

    'cod' => [
        // Enable COD
        'enabled' => true,

        // Maximum COD amount
        'max_amount' => 1000,

        // COD fee (percentage)
        'fee_percentage' => 0.015, // 1.5%

        // Minimum COD fee
        'minimum_fee' => 1.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Shipping API logging settings.
    |
    */

    'logging' => [
        'enabled' => true,
        'log_requests' => true,
        'log_responses' => true,
        'mask_sensitive' => true,
        'retention_days' => 90,
    ],

];
