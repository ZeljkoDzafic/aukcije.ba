<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->uuid('opened_by')->nullable();
            $table->foreign('opened_by')->references('id')->on('users')->onDelete('set null');
            $table->string('reason', 50);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('open');
            $table->text('resolution')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();
            $table->timestampTz('resolved_at')->nullable();
        });

        DB::statement("ALTER TABLE disputes ADD CONSTRAINT disputes_status_check CHECK (status IN ('open', 'in_review', 'resolved', 'closed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
