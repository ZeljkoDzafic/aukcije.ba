<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('messages')) {
            return;
        }

        Schema::table('messages', function (Blueprint $table) {
            if (! Schema::hasColumn('messages', 'message_type')) {
                $table->string('message_type', 20)->default('user')->after('auction_id');
            }

            if (! Schema::hasColumn('messages', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('content');
            }

            if (! Schema::hasColumn('messages', 'attachment_url')) {
                $table->text('attachment_url')->nullable()->after('attachment_name');
            }

            if (! Schema::hasColumn('messages', 'metadata')) {
                $table->json('metadata')->nullable()->after('attachment_url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('messages')) {
            return;
        }

        Schema::table('messages', function (Blueprint $table) {
            foreach (['message_type', 'attachment_name', 'attachment_url', 'metadata'] as $column) {
                if (Schema::hasColumn('messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
