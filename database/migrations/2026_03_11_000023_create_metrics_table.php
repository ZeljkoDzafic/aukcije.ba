<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'slo:bidding:p99'
            $table->decimal('value', 12, 4);
            $table->string('unit')->default('ms'); // ms, seconds, count, etc.
            $table->json('tags')->nullable(); // {endpoint: 'bidding', metric_type: 'slo'}
            $table->timestamps();

            // Indexes for querying
            $table->index('name');
            $table->index('created_at');
        });

        if (in_array($driver, ['mysql', 'pgsql'], true)) {
            Schema::table('metrics', function (Blueprint $table) {
                $table->index('tags'); // JSON index fallback only on engines that support it cleanly here.
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
