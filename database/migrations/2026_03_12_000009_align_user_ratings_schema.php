<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_ratings')) {
            return;
        }

        Schema::table('user_ratings', function (Blueprint $table) {
            if (! Schema::hasColumn('user_ratings', 'rated_id')) {
                $table->uuid('rated_id')->nullable()->after('rater_id');
                $table->foreign('rated_id')->references('id')->on('users')->onDelete('set null');
            }

            if (! Schema::hasColumn('user_ratings', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('type');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
