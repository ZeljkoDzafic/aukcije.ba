<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallets')) {
            return;
        }

        Schema::table('wallets', function (Blueprint $table) {
            if (! Schema::hasColumn('wallets', 'frozen')) {
                $table->boolean('frozen')->default(false);
            }

            if (! Schema::hasColumn('wallets', 'frozen_at')) {
                $table->timestamp('frozen_at')->nullable();
            }

            if (! Schema::hasColumn('wallets', 'frozen_reason')) {
                $table->text('frozen_reason')->nullable();
            }
        });

        if (Schema::hasColumn('wallets', 'is_frozen')) {
            DB::table('wallets')->update(['frozen' => DB::raw('is_frozen')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('wallets')) {
            return;
        }

        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasColumn('wallets', 'frozen_reason')) {
                $table->dropColumn('frozen_reason');
            }

            if (Schema::hasColumn('wallets', 'frozen_at')) {
                $table->dropColumn('frozen_at');
            }

            if (Schema::hasColumn('wallets', 'frozen')) {
                $table->dropColumn('frozen');
            }
        });
    }
};
