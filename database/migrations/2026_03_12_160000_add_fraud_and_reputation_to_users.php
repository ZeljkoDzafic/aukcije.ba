<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('fraud_score')->default(0)->after('trust_score');
            $table->decimal('seller_reputation_score', 5, 2)->nullable()->after('fraud_score');
            $table->timestamp('seller_reputation_updated_at')->nullable()->after('seller_reputation_score');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fraud_score', 'seller_reputation_score', 'seller_reputation_updated_at']);
        });
    }
};
