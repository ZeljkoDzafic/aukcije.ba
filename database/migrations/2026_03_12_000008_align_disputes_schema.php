<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'opened_by_id')) {
                $table->uuid('opened_by_id')->nullable()->after('order_id');
                $table->foreign('opened_by_id')->references('id')->on('users')->onDelete('set null');
            }

            if (! Schema::hasColumn('disputes', 'resolved_by_id')) {
                $table->uuid('resolved_by_id')->nullable()->after('resolution');
                $table->foreign('resolved_by_id')->references('id')->on('users')->onDelete('set null');
            }

            if (! Schema::hasColumn('disputes', 'seller_response')) {
                $table->text('seller_response')->nullable()->after('resolved_by');
            }

            if (! Schema::hasColumn('disputes', 'evidence')) {
                $table->json('evidence')->nullable()->after('seller_response');
            }

            if (! Schema::hasColumn('disputes', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }

            if (! Schema::hasColumn('disputes', 'escalated_at')) {
                $table->timestampTz('escalated_at')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
