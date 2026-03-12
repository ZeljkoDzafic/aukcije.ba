<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('condition', 20);
            $table->decimal('start_price', 12, 2);
            $table->decimal('current_price', 12, 2);
            $table->decimal('reserve_price', 12, 2)->nullable();
            $table->decimal('buy_now_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('BAM');
            $table->string('type', 20)->default('standard');
            $table->string('status', 20)->default('draft');
            $table->unsignedSmallInteger('duration_days')->default(7);
            $table->timestampTz('starts_at');
            $table->timestampTz('ends_at');
            $table->timestampTz('original_end_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('ended_at')->nullable();
            $table->boolean('auto_extension')->default(true);
            $table->integer('extension_minutes')->default(3);
            $table->string('location_city', 100)->nullable();
            $table->string('location_country', 50)->default('BA');
            $table->string('location', 255)->nullable();
            $table->boolean('shipping_available')->default(true);
            $table->decimal('shipping_cost', 8, 2)->nullable();
            $table->text('shipping_info')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('bids_count')->default(0);
            $table->integer('watchers_count')->default(0);
            $table->uuid('winner_id')->nullable();
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');
            $table->timestampTz('last_bid_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestampTz('featured_until')->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->index('seller_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('ends_at');
            $table->index('winner_id');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE auctions ADD CONSTRAINT auctions_condition_check CHECK (condition IN ('new', 'like_new', 'used', 'for_parts'))");
            DB::statement("ALTER TABLE auctions ADD CONSTRAINT auctions_type_check CHECK (type IN ('standard', 'buy_now', 'dutch'))");
            DB::statement("ALTER TABLE auctions ADD CONSTRAINT auctions_status_check CHECK (status IN ('draft', 'active', 'finished', 'cancelled', 'sold'))");
        }

        DB::statement("CREATE INDEX auctions_active_ends_at_idx ON auctions (ends_at) WHERE status = 'active'");
        DB::statement("CREATE INDEX auctions_is_featured_idx ON auctions (is_featured) WHERE is_featured = true");
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
