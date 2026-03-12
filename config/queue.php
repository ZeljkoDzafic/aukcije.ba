<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => env('QUEUE_CONNECTION', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'pgsql'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'pgsql'),
        'table' => 'failed_jobs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Horizon Configuration
    |--------------------------------------------------------------------------
    */

    'horizon' => [
        'domains' => [
            'production' => ['aukcije.ba', 'www.aukcije.ba'],
            'local' => ['localhost', 'aukcije.test'],
        ],
        
        'environments' => [
            'production' => [
                // Default queue - general jobs
                'supervisor-1' => [
                    'connection' => 'redis',
                    'queue' => ['default'],
                    'balance' => 'auto',
                    'autoScalingStrategy' => [
                        'balance' => 'auto',
                        'maxProcesses' => 8,
                        'balanceMaxShift' => 1,
                        'balanceCooldown' => 3,
                    ],
                    'minProcesses' => 2,
                    'maxProcesses' => 8,
                    'tries' => 3,
                    'timeout' => 60,
                ],
                // Notifications queue - email, SMS, push
                'supervisor-2' => [
                    'connection' => 'redis',
                    'queue' => ['emails', 'notifications', 'sms', 'push'],
                    'balance' => 'auto',
                    'autoScalingStrategy' => [
                        'balance' => 'auto',
                        'maxProcesses' => 4,
                        'balanceMaxShift' => 1,
                        'balanceCooldown' => 3,
                    ],
                    'minProcesses' => 1,
                    'maxProcesses' => 4,
                    'tries' => 3,
                    'timeout' => 120,
                ],
                // High priority queue - bidding, payments
                'supervisor-3' => [
                    'connection' => 'redis',
                    'queue' => ['high', 'bidding', 'payments'],
                    'balance' => 'auto',
                    'autoScalingStrategy' => [
                        'balance' => 'auto',
                        'maxProcesses' => 2,
                        'balanceMaxShift' => 1,
                        'balanceCooldown' => 3,
                    ],
                    'minProcesses' => 1,
                    'maxProcesses' => 2,
                    'tries' => 1,
                    'timeout' => 30,
                    'priority' => 1, // Higher priority
                ],
            ],

            'local' => [
                'supervisor-1' => [
                    'connection' => 'redis',
                    'queue' => ['default', 'emails', 'notifications', 'bidding', 'high'],
                    'balance' => 'auto',
                    'minProcesses' => 1,
                    'maxProcesses' => 4,
                    'tries' => 1,
                    'timeout' => 60,
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Horizon Alert Configuration
        |--------------------------------------------------------------------------
        */

        'alerts' => [
            'horizon' => [
                'failed_jobs' => [
                    'threshold' => 10,
                    'notification' => ['mail', 'slack'],
                ],
                'long_wait_times' => [
                    'threshold' => 60, // seconds
                    'notification' => ['mail', 'slack'],
                ],
                'job_failures' => [
                    'threshold' => 5,
                    'notification' => ['mail', 'slack'],
                ],
                'supervisor_lost' => [
                    'threshold' => 1,
                    'notification' => ['mail', 'slack'],
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Horizon Metrics Retention
        |--------------------------------------------------------------------------
        */

        'trim' => [
            'recent' => 60, // minutes
            'pending' => 60, // minutes
            'failed' => 1440, // minutes (24 hours)
            'monitored' => 1440, // minutes (24 hours)
        ],
    ],

];
