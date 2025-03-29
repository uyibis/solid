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
        Schema::create('shopify_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unique(); // Shopify order ID
            $table->string('email');
            $table->decimal('total_price', 10, 2); // Total price
            $table->string('currency'); // Currency// Order creation time
            $table->string('customer_name'); // Customer's full name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_orders');
    }
};
