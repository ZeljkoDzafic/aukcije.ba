<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auction_id')->nullable();
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('set null');
            $table->uuid('buyer_id')->nullable();
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('set null');
            $table->uuid('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->decimal('commission', 12, 2);
            $table->string('status', 30)->default('pending');
            $table->jsonb('shipping_address')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'payment_received', 'shipped', 'delivered', 'completed', 'disputed', 'cancelled'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
