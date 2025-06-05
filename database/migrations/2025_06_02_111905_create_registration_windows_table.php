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
        Schema::create('registration_windows', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->integer('max_registrations')->default(0);
            $table->integer('current_registrations')->default(0);
            $table->string('password')->nullable();
            $table->timestamp('first_use_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índices para otimização
            $table->index('is_active');
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_windows');
    }
};
