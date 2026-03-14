<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const ADMIN_PASSWORD = 'AdminPassword123!';

    private const DEFAULT_PASSWORD = 'Password123!';

    public function run(): void
    {
        // 1 Super Admin
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@aukcije.ba'],
            [
                'name' => 'Admin Aukcije',
                'password' => Hash::make(self::ADMIN_PASSWORD),
                'email_verified_at' => now(),
            ]
        );
        UserProfile::query()->updateOrCreate(
            ['user_id' => $admin->id],
            ['full_name' => 'Admin Aukcije', 'city' => 'Sarajevo']
        );
        Wallet::query()->updateOrCreate(['user_id' => $admin->id], ['balance' => 0]);
        $admin->assignRole('super_admin');

        // 2 Moderators
        foreach (['Moderator Jedan', 'Moderator Dva'] as $i => $name) {
            $mod = User::query()->updateOrCreate(
                ['email' => "moderator{$i}@aukcije.ba"],
                [
                    'name' => $name,
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'email_verified_at' => now(),
                ]
            );
            UserProfile::query()->updateOrCreate(
                ['user_id' => $mod->id],
                ['full_name' => $name, 'city' => 'Mostar']
            );
            Wallet::query()->updateOrCreate(['user_id' => $mod->id], ['balance' => 0]);
            $mod->assignRole('moderator');
        }

        // 5 Sellers (2 verified)
        $sellers = [
            ['name' => 'Mirza Prodavac',   'email' => 'mirza@seller.ba',   'city' => 'Sarajevo',   'verified' => true],
            ['name' => 'Amra Kolekcionar', 'email' => 'amra@seller.ba',    'city' => 'Banja Luka', 'verified' => true],
            ['name' => 'Edin Tehničar',    'email' => 'edin@seller.ba',    'city' => 'Tuzla',      'verified' => false],
            ['name' => 'Selma Moda',       'email' => 'selma@seller.ba',   'city' => 'Zenica',     'verified' => false],
            ['name' => 'Damir Auto',       'email' => 'damir@seller.ba',   'city' => 'Mostar',     'verified' => false],
        ];

        foreach ($sellers as $s) {
            $user = User::query()->updateOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'email_verified_at' => now(),
                ]
            );
            UserProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $s['name'], 'city' => $s['city']]
            );
            Wallet::query()->updateOrCreate(['user_id' => $user->id], ['balance' => rand(100, 500)]);
            $user->assignRole($s['verified'] ? 'verified_seller' : 'seller');
        }

        // 10 Buyers
        $buyers = [
            ['name' => 'Alen Kupac',      'email' => 'alen@buyer.ba',   'city' => 'Sarajevo'],
            ['name' => 'Lejla Šopingica', 'email' => 'lejla@buyer.ba',  'city' => 'Banja Luka'],
            ['name' => 'Haris Kolektar',  'email' => 'haris@buyer.ba',  'city' => 'Tuzla'],
            ['name' => 'Maja Licitator',  'email' => 'maja@buyer.ba',   'city' => 'Zenica'],
            ['name' => 'Nedim Džeparac', 'email' => 'nedim@buyer.ba',  'city' => 'Mostar'],
            ['name' => 'Sara Povoljnica', 'email' => 'sara@buyer.ba',   'city' => 'Sarajevo'],
            ['name' => 'Kemal Bider',     'email' => 'kemal@buyer.ba',  'city' => 'Travnik'],
            ['name' => 'Dina Aukcionar',  'email' => 'dina@buyer.ba',   'city' => 'Brčko'],
            ['name' => 'Tarik Entuzijast', 'email' => 'tarik@buyer.ba',  'city' => 'Sarajevo'],
            ['name' => 'Nela Bargain',    'email' => 'nela@buyer.ba',   'city' => 'Bihać'],
        ];

        foreach ($buyers as $b) {
            $user = User::query()->updateOrCreate(
                ['email' => $b['email']],
                [
                    'name' => $b['name'],
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'email_verified_at' => now(),
                ]
            );
            UserProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $b['name'], 'city' => $b['city']]
            );
            Wallet::query()->updateOrCreate(['user_id' => $user->id], ['balance' => rand(50, 300)]);
            $user->assignRole('buyer');
        }

        $this->command->info('Users seeded: 1 admin, 2 moderators, 5 sellers, 10 buyers.');
    }
}
