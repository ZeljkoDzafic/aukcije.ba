<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            foreach ([
                'total_amount' => fn () => $table->decimal('total_amount', 12, 2)->nullable()->after('commission'),
                'commission_amount' => fn () => $table->decimal('commission_amount', 12, 2)->nullable()->after('total_amount'),
                'seller_payout' => fn () => $table->decimal('seller_payout', 12, 2)->nullable()->after('commission_amount'),
                'payment_status' => fn () => $table->string('payment_status', 30)->default('pending')->after('status'),
                'payment_gateway' => fn () => $table->string('payment_gateway', 50)->nullable()->after('payment_status'),
                'paid_at' => fn () => $table->timestampTz('paid_at')->nullable()->after('payment_gateway'),
                'payment_deadline_at' => fn () => $table->timestampTz('payment_deadline_at')->nullable()->after('paid_at'),
                'shipping_method' => fn () => $table->string('shipping_method', 50)->nullable()->after('payment_deadline_at'),
                'shipping_cost' => fn () => $table->decimal('shipping_cost', 12, 2)->nullable()->after('shipping_method'),
                'shipping_city' => fn () => $table->string('shipping_city', 100)->nullable()->after('shipping_address'),
                'shipping_postal_code' => fn () => $table->string('shipping_postal_code', 20)->nullable()->after('shipping_city'),
                'shipping_country' => fn () => $table->string('shipping_country', 10)->nullable()->after('shipping_postal_code'),
                'delivered_at' => fn () => $table->timestampTz('delivered_at')->nullable()->after('shipping_country'),
                'completed_at' => fn () => $table->timestampTz('completed_at')->nullable()->after('delivered_at'),
                'cancelled_at' => fn () => $table->timestampTz('cancelled_at')->nullable()->after('completed_at'),
                'cancel_reason' => fn () => $table->text('cancel_reason')->nullable()->after('cancelled_at'),
            ] as $column => $definition) {
                if (! Schema::hasColumn('orders', $column)) {
                    $definition();
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
