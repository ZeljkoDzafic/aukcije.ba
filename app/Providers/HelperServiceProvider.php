<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load helper files
        $helpers = config('app.helpers', []);

        foreach ($helpers as $helper) {
            if (file_exists($helper)) {
                require_once $helper;
            }
        }
    }
}
