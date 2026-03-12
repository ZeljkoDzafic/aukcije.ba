<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            // scheduled: auction created but not yet started (future start time)
            // 'scheduled' will be stored as string in status column (already a string)
            $table->boolean('is_reserve_public')->default(false)->after('reserve_price')
                ->comment('Whether to show reserve price indicator to bidders');
            $table->boolean('reserve_met')->default(false)->after('is_reserve_public');
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn(['is_reserve_public', 'reserve_met']);
        });
    }
};
