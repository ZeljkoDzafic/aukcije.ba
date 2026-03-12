<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auction_watchers') || Schema::hasColumn('auction_watchers', 'updated_at')) {
            return;
        }

        Schema::table('auction_watchers', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('auction_watchers') || ! Schema::hasColumn('auction_watchers', 'updated_at')) {
            return;
        }

        Schema::table('auction_watchers', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
