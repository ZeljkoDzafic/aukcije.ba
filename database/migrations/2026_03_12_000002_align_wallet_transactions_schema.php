<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('wallet_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }

            if (! Schema::hasColumn('wallet_transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('reference_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('wallet_transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
