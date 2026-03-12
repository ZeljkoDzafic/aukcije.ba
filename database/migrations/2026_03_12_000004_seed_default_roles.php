<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        $now = now();

        foreach (['buyer', 'seller', 'verified_seller', 'admin', 'moderator', 'super_admin'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role, 'guard_name' => 'web'],
                ['created_at' => $now, 'updated_at' => $now],
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->whereIn('name', ['buyer', 'seller', 'verified_seller', 'admin', 'moderator', 'super_admin'])
            ->where('guard_name', 'web')
            ->delete();
    }
};
