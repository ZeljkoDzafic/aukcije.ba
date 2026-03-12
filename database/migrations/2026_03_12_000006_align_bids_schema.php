<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bids')) {
            return;
        }

        Schema::table('bids', function (Blueprint $table) {
            if (! Schema::hasColumn('bids', 'is_winning')) {
                $table->boolean('is_winning')->default(false)->after('amount');
            }

            if (! Schema::hasColumn('bids', 'max_proxy_amount')) {
                $table->decimal('max_proxy_amount', 12, 2)->nullable()->after('is_auto');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bids')) {
            return;
        }

        Schema::table('bids', function (Blueprint $table) {
            if (Schema::hasColumn('bids', 'max_proxy_amount')) {
                $table->dropColumn('max_proxy_amount');
            }

            if (Schema::hasColumn('bids', 'is_winning')) {
                $table->dropColumn('is_winning');
            }
        });
    }
};
