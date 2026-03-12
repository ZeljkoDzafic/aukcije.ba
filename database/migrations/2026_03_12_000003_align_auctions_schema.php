<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auctions')) {
            return;
        }

        Schema::table('auctions', function (Blueprint $table) {
            if (! Schema::hasColumn('auctions', 'duration_days')) {
                $table->unsignedSmallInteger('duration_days')->default(7)->after('status');
            }

            if (! Schema::hasColumn('auctions', 'started_at')) {
                $table->timestampTz('started_at')->nullable()->after('original_end_at');
            }

            if (! Schema::hasColumn('auctions', 'ended_at')) {
                $table->timestampTz('ended_at')->nullable()->after('started_at');
            }

            if (! Schema::hasColumn('auctions', 'location')) {
                $table->string('location', 255)->nullable()->after('location_country');
            }

            if (! Schema::hasColumn('auctions', 'shipping_info')) {
                $table->text('shipping_info')->nullable()->after('shipping_cost');
            }

            if (! Schema::hasColumn('auctions', 'watchers_count')) {
                $table->integer('watchers_count')->default(0)->after('bids_count');
            }

            if (! Schema::hasColumn('auctions', 'winner_id')) {
                $table->uuid('winner_id')->nullable()->after('watchers_count');
                $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');
            }

            if (! Schema::hasColumn('auctions', 'last_bid_at')) {
                $table->timestampTz('last_bid_at')->nullable()->after('winner_id');
            }

            if (! Schema::hasColumn('auctions', 'featured_until')) {
                $table->timestampTz('featured_until')->nullable()->after('is_featured');
            }

            if (! Schema::hasColumn('auctions', 'slug')) {
                $table->string('slug', 255)->nullable()->unique()->after('featured_until');
            }

            if (! Schema::hasColumn('auctions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
