<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bid_increments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('price_from', 12, 2);
            $table->decimal('price_to', 12, 2)->nullable();
            $table->decimal('increment', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bid_increments');
    }
};
