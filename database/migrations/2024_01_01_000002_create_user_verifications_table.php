<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type', 50);
            $table->string('status', 20)->default('pending');
            $table->text('document_url')->nullable();
            $table->timestampTz('verified_at')->nullable();
            $table->uuid('reviewer_id')->nullable();
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE user_verifications ADD CONSTRAINT user_verifications_type_check CHECK (type IN ('phone_sms', 'id_document', 'address_proof'))");
            DB::statement("ALTER TABLE user_verifications ADD CONSTRAINT user_verifications_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
