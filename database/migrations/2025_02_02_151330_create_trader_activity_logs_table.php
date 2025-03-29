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
        Schema::create('trader_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained()->onDelete('cascade'); // Links to Trader model
            $table->string('ip_address'); // IP address of the bot
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trader_activity_logs');
    }
};
