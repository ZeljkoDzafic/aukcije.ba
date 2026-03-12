<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shipments') || Schema::hasColumn('shipments', 'updated_at')) {
            return;
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('shipments') || ! Schema::hasColumn('shipments', 'updated_at')) {
            return;
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
