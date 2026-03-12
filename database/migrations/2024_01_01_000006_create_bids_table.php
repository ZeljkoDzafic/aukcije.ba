<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auction_id');
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_proxy')->default(false);
            $table->boolean('is_auto')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->index('auction_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        DB::statement('CREATE INDEX bids_auction_amount_idx ON bids (auction_id, amount DESC)');
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
