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
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('commission_amount', 12, 2)->nullable();
            $table->decimal('seller_payout', 12, 2)->nullable();
            $table->string('status', 30)->default('pending');
            $table->string('payment_status', 30)->default('pending');
            $table->string('payment_gateway', 50)->nullable();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('payment_deadline_at')->nullable();
            $table->string('shipping_method', 50)->nullable();
            $table->decimal('shipping_cost', 12, 2)->nullable();
            $table->jsonb('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_country', 10)->nullable();
            $table->timestampTz('delivered_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'payment_received', 'shipped', 'delivered', 'completed', 'disputed', 'cancelled'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
