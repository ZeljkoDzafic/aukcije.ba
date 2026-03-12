<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * T-1504: Critical DB performance indices.
 * These cover the most frequent queries in the bidding, listing, and order flows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->index(['auction_id', 'created_at'], 'idx_bids_auction_created');
            $table->index(['auction_id', 'is_winning'], 'idx_bids_auction_winning');
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->index(['status', 'ends_at'], 'idx_auctions_status_ends');
            $table->index(['seller_id', 'status'], 'idx_auctions_seller_status');
            $table->index(['is_featured', 'status', 'ends_at'], 'idx_auctions_featured');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['wallet_id', 'created_at'], 'idx_wallet_tx_created');
        });

        Schema::table('auction_watchers', function (Blueprint $table) {
            $table->index('user_id', 'idx_watchers_user');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['buyer_id', 'status'], 'idx_orders_buyer_status');
            $table->index(['seller_id', 'status'], 'idx_orders_seller_status');
        });
    }

    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('idx_bids_auction_created');
            $table->dropIndex('idx_bids_auction_winning');
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->dropIndex('idx_auctions_status_ends');
            $table->dropIndex('idx_auctions_seller_status');
            $table->dropIndex('idx_auctions_featured');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_wallet_tx_created');
        });

        Schema::table('auction_watchers', function (Blueprint $table) {
            $table->dropIndex('idx_watchers_user');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_buyer_status');
            $table->dropIndex('idx_orders_seller_status');
        });
    }
};
