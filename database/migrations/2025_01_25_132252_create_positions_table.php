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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // "Position"
            $table->string('master_id')->index();
            $table->string('instrument');
            $table->string('market_position'); // "Long" or "Short"
            $table->integer('quantity');
            $table->decimal('average_price', 10, 2);
            $table->decimal('unrealized_pnl', 10, 2);
            $table->decimal('stop_loss', 10, 2)->nullable();
            $table->decimal('take_profit', 10, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open'); // New status field
            $table->timestamp('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
