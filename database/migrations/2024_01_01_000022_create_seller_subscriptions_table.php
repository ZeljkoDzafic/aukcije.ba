<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('tier', 20)->default('free');
            $table->timestampTz('starts_at');
            $table->timestampTz('ends_at')->nullable();
            $table->boolean('is_trial')->default(false);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE seller_subscriptions ADD CONSTRAINT seller_subscriptions_tier_check CHECK (tier IN ('free', 'premium', 'storefront'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_subscriptions');
    }
};
