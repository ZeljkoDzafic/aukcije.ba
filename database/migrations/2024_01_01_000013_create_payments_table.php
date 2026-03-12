<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->uuid('order_id')->nullable();
            $table->string('gateway', 50);
            $table->string('gateway_transaction_id', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BAM');
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_gateway_check CHECK (gateway IN ('stripe', 'paypal', 'monri', 'corvuspay', 'wallet'))");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'completed', 'failed', 'refunded'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
