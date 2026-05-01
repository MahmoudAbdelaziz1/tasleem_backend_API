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
        Schema::create('products', function (Blueprint $table) {
         $table->id();
            $table->string('name');
            $table->text('description')->nullable();  
            $table->float('price', 12, 2)->default(0);  
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');  
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');  
            $table->integer('quantity')->default(1);  
            $table->integer('view_count')->default(0);
            $table->float('rate', 3, 2)->default(0);  
            $table->integer('pay_count')->default(0);
            $table->integer('addingToCart_count')->default(0);
            $table->enum('status', ["1","0"])->default('1');  
            $table->enum('type', ['sale', 'rental'])->default('sale'); 
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('owner_id');
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
