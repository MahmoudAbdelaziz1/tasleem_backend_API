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
        Schema::create('logs', function (Blueprint $table) {
            $table->id('log_id'); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->string('action_type', 50);  
            $table->string('action_name', 100);
            $table->string('module', 50);  
            $table->string('entity_type', 50)->nullable();  
            $table->unsignedBigInteger('entity_id')->nullable();  
            $table->json('old_data')->nullable();  
            $table->json('new_data')->nullable();  
            $table->string('ip_address', 45)->nullable(); 
            $table->text('user_agent')->nullable();  
            $table->string('status', 20)->default('success');  
            $table->text('message')->nullable();  
            $table->string('error_code', 50)->nullable();  
            $table->timestamps();  

            
            $table->index('user_id');
            $table->index('action_type');
            $table->index('module');
            $table->index('entity_type');
            $table->index('entity_id');
            $table->index('status');
            $table->index('created_at');

           
            $table->index(['module', 'action_type', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
