<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserVerification;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestAccessSeeder extends Seeder
{
    private const PASSWORD = 'Test12345!';

    private const DOCS_PASSWORD = 'Password123!';

    public function run(): void
    {
        $users = [
            [
                'roles' => ['super_admin'],
                'name' => 'Test Super Admin',
                'email' => 'test.superadmin@aukcije.ba',
                'city' => 'Sarajevo',
                'wallet' => 0,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 3,
                'phone' => '+38761111001',
            ],
            [
                'roles' => ['moderator'],
                'name' => 'Test Moderator',
                'email' => 'test.moderator@aukcije.ba',
                'city' => 'Mostar',
                'wallet' => 0,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 2,
                'phone' => '+38761111002',
            ],
            [
                'roles' => ['buyer', 'seller', 'verified_seller'],
                'name' => 'Test Verified Seller',
                'email' => 'test.verified-seller@aukcije.ba',
                'city' => 'Banja Luka',
                'wallet' => 1250,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 3,
                'phone' => '+38761111003',
            ],
            [
                'roles' => ['buyer', 'seller'],
                'name' => 'Test Seller',
                'email' => 'test.seller@aukcije.ba',
                'city' => 'Tuzla',
                'wallet' => 420,
                'email_verified' => true,
                'phone_verified' => false,
                'kyc_level' => 2,
                'phone' => '+38761111004',
            ],
            [
                'roles' => ['buyer', 'seller'],
                'name' => 'Demo Seller',
                'email' => 'seller@test.com',
                'city' => 'Sarajevo',
                'wallet' => 600,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 2,
                'phone' => '+38761111014',
                'password' => self::DOCS_PASSWORD,
            ],
            [
                'roles' => ['buyer'],
                'name' => 'Test Buyer',
                'email' => 'test.buyer@aukcije.ba',
                'city' => 'Zenica',
                'wallet' => 780,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 1,
                'phone' => '+38761111005',
            ],
            [
                'roles' => ['buyer'],
                'name' => 'Demo Buyer',
                'email' => 'buyer@test.com',
                'city' => 'Mostar',
                'wallet' => 350,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 1,
                'phone' => '+38761111015',
                'password' => self::DOCS_PASSWORD,
            ],
            [
                'roles' => ['buyer'],
                'name' => 'Test Banned Buyer',
                'email' => 'test.banned@aukcije.ba',
                'city' => 'Bihać',
                'wallet' => 150,
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_level' => 1,
                'phone' => '+38761111006',
                'is_banned' => true,
                'ban_reason' => 'Seeded banned user for moderation tests',
            ],
            [
                'roles' => ['buyer'],
                'name' => 'Test Unverified Buyer',
                'email' => 'test.unverified@aukcije.ba',
                'city' => 'Travnik',
                'wallet' => 90,
                'email_verified' => false,
                'phone_verified' => false,
                'kyc_level' => 0,
                'phone' => '+38761111007',
            ],
        ];

        foreach ($users as $definition) {
            $user = User::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'password' => Hash::make($definition['password'] ?? self::PASSWORD),
                    'phone' => $definition['phone'],
                    'email_verified_at' => $definition['email_verified'] ? now() : null,
                    'phone_verified_at' => $definition['phone_verified'] ? now() : null,
                    'kyc_level' => $definition['kyc_level'],
                    'trust_score' => 4.8,
                    'is_banned' => $definition['is_banned'] ?? false,
                    'banned_at' => ($definition['is_banned'] ?? false) ? now() : null,
                    'ban_reason' => $definition['ban_reason'] ?? null,
                ]
            );

            $user->syncRoles($definition['roles']);

            $profilePayload = [
                'full_name' => $definition['name'],
                'city' => $definition['city'],
                'country' => 'BiH',
                'phone' => $definition['phone'],
            ];

            if (Schema::hasColumn('user_profiles', 'preferred_language')) {
                $profilePayload['preferred_language'] = 'bs';
            }

            if (Schema::hasColumn('user_profiles', 'language')) {
                $profilePayload['language'] = 'bs';
            }

            if (Schema::hasColumn('user_profiles', 'currency')) {
                $profilePayload['currency'] = 'BAM';
            }

            if (Schema::hasColumn('user_profiles', 'timezone')) {
                $profilePayload['timezone'] = 'Europe/Sarajevo';
            }

            UserProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                $profilePayload
            );

            Wallet::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => $definition['wallet'],
                    'escrow_balance' => 0,
                    'frozen' => false,
                ]
            );

            if ($definition['kyc_level'] >= 1) {
                UserVerification::query()->updateOrCreate(
                    ['user_id' => $user->id, 'type' => 'phone_sms'],
                    ['status' => 'approved', 'verified_at' => now()]
                );
            }

            if ($definition['kyc_level'] >= 2) {
                UserVerification::query()->updateOrCreate(
                    ['user_id' => $user->id, 'type' => 'id_document'],
                    ['status' => 'approved', 'verified_at' => now()]
                );
            }

            if ($definition['kyc_level'] >= 3) {
                UserVerification::query()->updateOrCreate(
                    ['user_id' => $user->id, 'type' => 'address_proof'],
                    ['status' => 'approved', 'verified_at' => now()]
                );
            }
        }

        $this->command?->info('TestAccessSeeder completed.');
        $this->command?->line('Shared password for all test users: '.self::PASSWORD);
    }
}
