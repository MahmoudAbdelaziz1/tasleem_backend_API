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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');  
            $table->foreignId('order_id')->nullable()->constrained('orders', 'order_id')->onDelete('set null'); 
            $table->foreignId('rental_id')->nullable()->constrained('rentals', 'rental_id')->onDelete('set null');  
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  
            $table->decimal('amount', 12, 2);  
            $table->enum('payment_method', ['credit_card', 'paypal', 'bank_transfer', 'cash']);  
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->unique()->nullable(); 
            $table->timestamps();  

  
          

            $table->index('user_id');
            $table->index('order_id');
            $table->index('rental_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
