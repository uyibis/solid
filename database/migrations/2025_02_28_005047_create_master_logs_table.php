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
        Schema::create('master_logs', function (Blueprint $table) {
            $table->id();
            $table->string('master_id'); // UUID for MasterId
            $table->enum('status', ['open', 'close'])->default('open'); // Status field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_logs');
    }
};
