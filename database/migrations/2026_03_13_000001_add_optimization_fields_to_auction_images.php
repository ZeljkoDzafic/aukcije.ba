<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_images', function (Blueprint $table) {
            $table->string('blurhash', 100)->nullable()->after('is_primary');
            $table->json('optimized_urls')->nullable()->after('blurhash');
            $table->unsignedSmallInteger('width')->nullable()->after('optimized_urls');
            $table->unsignedSmallInteger('height')->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('auction_images', function (Blueprint $table) {
            $table->dropColumn(['blurhash', 'optimized_urls', 'width', 'height']);
        });
    }
};
