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
        Schema::create('cart_items', function (Blueprint $table) {
             $table->id('cart_item_id');  
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); 
            $table->integer('quantity')->default(1);  
            $table->date('rental_start_date')->nullable(); 
            $table->date('rental_end_date')->nullable();  
            $table->enum('item_type', ['purchase', 'rental'])->default('purchase'); 
            $table->timestamps();  

          

          
            $table->unique(['user_id', 'product_id', 'item_type']);

            $table->index('user_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
