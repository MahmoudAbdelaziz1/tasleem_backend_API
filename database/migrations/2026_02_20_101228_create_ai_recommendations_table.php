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
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id('rec_id'); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');  
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');  
            $table->decimal('score', 5, 4);  
            $table->enum('algorithm_type', ['collaborative', 'content', 'hybrid', 'popularity', 'location']);  
            $table->text('reason')->nullable();  
            $table->json('metadata')->nullable();  
            $table->timestamp('expires_at')->nullable(); 
            $table->timestamps(); 

            
            $table->unique(['user_id', 'product_id', 'algorithm_type'], 'unique_recommendation');

            $table->index('user_id');
            $table->index('product_id');
            $table->index('score');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
