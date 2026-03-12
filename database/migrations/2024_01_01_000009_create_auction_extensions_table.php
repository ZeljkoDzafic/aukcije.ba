<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_extensions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auction_id');
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->uuid('triggered_by_bid_id')->nullable();
            $table->foreign('triggered_by_bid_id')->references('id')->on('bids')->onDelete('set null');
            $table->timestampTz('old_end_at');
            $table->timestampTz('new_end_at');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_extensions');
    }
};
