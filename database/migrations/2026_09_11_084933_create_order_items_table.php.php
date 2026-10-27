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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();

            // បង្កើត Foreign Key Constraint ដាច់ដោយឡែកដើម្បីកុំឱ្យទាក់ Error 1005
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};// database/migrations/xxxx_xx_xx_create_orders_table.php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_no')->unique();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount_percent', 5, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('total_price', 10, 2);
    $table->string('payment_method')->default('cash'); // cash, khqr
    $table->string('status')->default('completed');
    $table->timestamps();
});

// database/migrations/xxxx_xx_xx_create_order_items_table.php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
    $table->string('product_name');
    $table->decimal('unit_price', 10, 2);
    $table->integer('quantity');
    $table->decimal('subtotal', 10, 2);
    $table->timestamps();
});