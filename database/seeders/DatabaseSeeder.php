<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            BidIncrementSeeder::class,
            UserSeeder::class,
            TestAccessSeeder::class,
            FeatureFlagSeeder::class,
            ContentPageSeeder::class,
            NewsArticleSeeder::class,
            AuctionSeeder::class,
        ]);
    }
}
