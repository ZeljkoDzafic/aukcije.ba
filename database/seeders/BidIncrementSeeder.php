<?php

namespace Database\Seeders;

use App\Models\BidIncrement;
use Illuminate\Database\Seeder;

class BidIncrementSeeder extends Seeder
{
    public function run(): void
    {
        BidIncrement::truncate();
        $increments = [
            ['price_from' => 0,    'price_to' => 10,   'increment' => 0.50],
            ['price_from' => 10,   'price_to' => 50,   'increment' => 1.00],
            ['price_from' => 50,   'price_to' => 100,  'increment' => 2.00],
            ['price_from' => 100,  'price_to' => 500,  'increment' => 5.00],
            ['price_from' => 500,  'price_to' => 1000, 'increment' => 10.00],
            ['price_from' => 1000, 'price_to' => 5000, 'increment' => 25.00],
            ['price_from' => 5000, 'price_to' => null,  'increment' => 50.00],
        ];
        foreach ($increments as $row) {
            BidIncrement::create($row);
        }
        $this->command->info('Bid increments seeded.');
    }
}
