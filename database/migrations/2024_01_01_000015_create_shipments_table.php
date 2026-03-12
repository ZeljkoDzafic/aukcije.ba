<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('courier', 50)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->text('waybill_url')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestampTz('shipped_at')->nullable();
            $table->timestampTz('delivered_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE shipments ADD CONSTRAINT shipments_courier_check CHECK (courier IN ('euroexpress', 'postexpress', 'overseas', 'bh_posta', 'other'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
