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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id'); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');  
            $table->integer('quantity')->default(1);  
            $table->decimal('unit_price', 12, 2);  
            $table->decimal('total_price', 12, 2);  
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending'); 
            $table->timestamps();  

            $table->index('user_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
