<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type', 50);
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->jsonb('data')->nullable();
            $table->timestampTz('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('notifications_custom', function (Blueprint $table) {
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_custom');
    }
};
