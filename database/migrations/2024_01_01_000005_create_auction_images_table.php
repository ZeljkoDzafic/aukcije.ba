<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auction_id');
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->text('url');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('auction_images', function (Blueprint $table) {
            $table->index('auction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_images');
    }
};
