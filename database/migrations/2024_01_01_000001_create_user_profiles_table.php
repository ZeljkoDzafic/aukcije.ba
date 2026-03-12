<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('full_name', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('avatar_url')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 50)->default('BA');
            $table->text('bio')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('preferred_language', 5)->default('bs');
            $table->jsonb('notification_preferences')->default('{"email": true, "push": true, "sms": false}');
            $table->timestamps();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
