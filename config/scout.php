<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    */

    'driver' => env('SCOUT_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    */

    'prefix' => env('SCOUT_PREFIX', 'aukcije_'),

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            'aukcije_auctions' => [
                'filterableAttributes' => [
                    'status',
                    'category_id',
                    'type',
                    'condition',
                    'price',
                    'country',
                    'city',
                    'seller_id',
                ],
                'sortableAttributes' => [
                    'ends_at',
                    'current_price',
                    'bids_count',
                    'created_at',
                ],
                'searchableAttributes' => [
                    'title',
                    'description',
                    'category_name',
                    'city',
                ],
                'typoTolerance' => [
                    'enabled' => true,
                    'minWordSizeForTypos' => [
                        'oneTypo' => 4,
                        'twoTypos' => 8,
                    ],
                ],
                'synonyms' => [
                    'mobitel' => ['telefon', 'mobilni', 'smartphone'],
                    'auto' => ['automobil', 'vozilo'],
                    'laptop' => ['notebook', 'prijenosno računalo'],
                    'tv' => ['televizor', 'televizija'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration (alternative)
    |--------------------------------------------------------------------------
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    */

    'identify' => true,

];
