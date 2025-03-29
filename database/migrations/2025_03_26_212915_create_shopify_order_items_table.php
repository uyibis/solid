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
        Schema::create('shopify_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id'); // Foreign key to the orders table
        $table->bigInteger('product_id'); // Shopify product ID
        $table->string('name'); // Product name
        $table->integer('quantity'); // Product quantity
        $table->decimal('price', 10, 2); // Product price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_order_items');
    }
};
