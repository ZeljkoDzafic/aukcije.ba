<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->uuid('rater_id')->nullable();
            $table->foreign('rater_id')->references('id')->on('users')->onDelete('set null');
            $table->uuid('rated_user_id')->nullable();
            $table->foreign('rated_user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('score');
            $table->text('comment')->nullable();
            $table->string('type', 10);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['order_id', 'rater_id']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE user_ratings ADD CONSTRAINT user_ratings_score_check CHECK (score BETWEEN 1 AND 5)');
            DB::statement("ALTER TABLE user_ratings ADD CONSTRAINT user_ratings_type_check CHECK (type IN ('buyer', 'seller'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ratings');
    }
};
