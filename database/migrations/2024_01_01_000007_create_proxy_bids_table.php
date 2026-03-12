<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proxy_bids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auction_id');
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('max_amount', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['auction_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proxy_bids');
    }
};
