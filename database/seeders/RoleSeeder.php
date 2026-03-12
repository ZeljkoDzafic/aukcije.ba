<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Auction permissions
            'auctions.view', 'auctions.create', 'auctions.edit', 'auctions.delete', 'auctions.publish',
            'auctions.feature', 'auctions.moderate',
            // Bid permissions
            'bids.place', 'bids.view',
            // Order permissions
            'orders.view', 'orders.manage', 'orders.ship',
            // Wallet permissions
            'wallet.view', 'wallet.deposit', 'wallet.withdraw',
            // KYC permissions
            'kyc.view', 'kyc.submit', 'kyc.approve', 'kyc.reject',
            // Rating permissions
            'ratings.give', 'ratings.view',
            // Dispute permissions
            'disputes.open', 'disputes.view', 'disputes.resolve', 'disputes.manage',
            // User management
            'users.view', 'users.ban', 'users.manage',
            // Category management
            'categories.view', 'categories.manage',
            // Message permissions
            'messages.send', 'messages.view',
            // Admin permissions
            'admin.access', 'admin.analytics', 'admin.settings',
            // Feature flags
            'features.manage',
            // API access
            'api.access',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles with permissions
        $buyer = Role::firstOrCreate(['name' => 'buyer', 'guard_name' => 'web']);
        $buyer->syncPermissions([
            'auctions.view', 'bids.place', 'bids.view',
            'orders.view', 'wallet.view', 'wallet.deposit', 'wallet.withdraw',
            'kyc.submit', 'ratings.give', 'ratings.view',
            'disputes.open', 'disputes.view', 'messages.send', 'messages.view',
        ]);

        $seller = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);
        $seller->syncPermissions([
            'auctions.view', 'auctions.create', 'auctions.edit', 'auctions.delete', 'auctions.publish',
            'bids.view', 'orders.view', 'orders.manage', 'orders.ship',
            'wallet.view', 'wallet.deposit', 'wallet.withdraw',
            'kyc.submit', 'ratings.give', 'ratings.view',
            'disputes.open', 'disputes.view', 'messages.send', 'messages.view',
        ]);

        $verifiedSeller = Role::firstOrCreate(['name' => 'verified_seller', 'guard_name' => 'web']);
        $verifiedSeller->syncPermissions([
            'auctions.view', 'auctions.create', 'auctions.edit', 'auctions.delete', 'auctions.publish',
            'bids.view', 'orders.view', 'orders.manage', 'orders.ship',
            'wallet.view', 'wallet.deposit', 'wallet.withdraw',
            'kyc.submit', 'ratings.give', 'ratings.view',
            'disputes.open', 'disputes.view', 'messages.send', 'messages.view',
            'api.access',
        ]);

        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $moderator->syncPermissions([
            'auctions.view', 'auctions.moderate', 'auctions.feature',
            'users.view', 'users.ban',
            'categories.view', 'categories.manage',
            'disputes.view', 'disputes.manage', 'disputes.resolve',
            'kyc.view', 'kyc.approve', 'kyc.reject',
            'admin.access', 'admin.analytics',
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $this->command->info('Roles and permissions seeded.');
    }
}
