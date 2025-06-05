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
        Schema::create('area_of_interests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índice para otimização
            $table->index('is_active');
        });
        
        // Tabela pivot para relacionamento muitos-para-muitos entre users e areas_of_interest
        Schema::create('user_area_of_interest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_of_interest_id')->constrained('area_of_interests')->onDelete('cascade');
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['user_id', 'area_of_interest_id']);
            
            // Garantir que cada combinação user_id e area_of_interest_id é única
            $table->unique(['user_id', 'area_of_interest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_area_of_interest');
        Schema::dropIfExists('area_of_interests');
    }
};
