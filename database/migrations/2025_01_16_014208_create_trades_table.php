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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->nullable();
            $table->string('instrument');
            $table->timestamp('execution_time');
            $table->decimal('execution_price', 15, 2);
            $table->integer('quantity');
            $table->string('market_position');
            $table->string('order_action');
            $table->string('order_type');
            $table->string('account_name');
            $table->string('trade_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
