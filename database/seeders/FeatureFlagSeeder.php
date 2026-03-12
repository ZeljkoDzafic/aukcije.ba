<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['name' => 'proxy_bidding',       'is_active' => true,  'description' => 'Automatsko licitiranje do maksimalnog iznosa'],
            ['name' => 'anti_sniping',         'is_active' => true,  'description' => 'Automatsko produženje aukcije u zadnjim minutama'],
            ['name' => 'escrow_protection',    'is_active' => true,  'description' => 'Zaštita plaćanja putem escrow sistema'],
            ['name' => 'rating_system',        'is_active' => true,  'description' => 'Sistem ocjenjivanja kupaca i prodavaca'],
            ['name' => 'dispute_resolution',   'is_active' => true,  'description' => 'Sistem za rješavanje sporova'],
            ['name' => 'wallet_system',        'is_active' => true,  'description' => 'Interni novčanik platforma'],
            ['name' => 'stripe_payments',      'is_active' => true,  'description' => 'Plaćanje putem Stripe (EUR/USD)'],
            ['name' => 'monri_payments',       'is_active' => false, 'description' => 'Plaćanje putem Monri (BAM)'],
            ['name' => 'corvuspay_payments',   'is_active' => false, 'description' => 'Plaćanje putem CorvusPay (HR)'],
            ['name' => 'euroexpress_shipping', 'is_active' => true,  'description' => 'Dostava putem EuroExpress kurira'],
            ['name' => 'postexpress_shipping', 'is_active' => false, 'description' => 'Dostava putem PostExpress kurira'],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(['name' => $flag['name']], $flag);
        }

        $this->command->info('Feature flags seeded.');
    }
}
