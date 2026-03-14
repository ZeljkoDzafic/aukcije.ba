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
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id', 255)->nullable()->after('gateway');
            }

            if (! Schema::hasColumn('payments', 'error_message')) {
                $table->text('error_message')->nullable()->after('status');
            }

            if (! Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable()->after('error_message');
            }

            if (! Schema::hasColumn('payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('metadata');
            }
        });

        if (Schema::hasColumn('payments', 'gateway_transaction_id') && Schema::hasColumn('payments', 'transaction_id')) {
            DB::table('payments')
                ->whereNull('transaction_id')
                ->whereNotNull('gateway_transaction_id')
                ->update(['transaction_id' => DB::raw('gateway_transaction_id')]);
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'gateway_response')) {
                $table->dropColumn('gateway_response');
            }

            if (Schema::hasColumn('payments', 'metadata')) {
                $table->dropColumn('metadata');
            }

            if (Schema::hasColumn('payments', 'error_message')) {
                $table->dropColumn('error_message');
            }

            if (Schema::hasColumn('payments', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
        });
    }
};
