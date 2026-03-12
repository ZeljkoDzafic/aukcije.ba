<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->string('action', 100);
            $table->string('target_type', 50)->nullable();
            $table->uuid('target_id')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
