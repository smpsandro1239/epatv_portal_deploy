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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('role', ['superadmin', 'admin', 'student'])->default('student');
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('approved');
            
            // Campos específicos para ex-alunos
            $table->integer('course_completion_year')->nullable();
            $table->string('cv')->nullable();
            $table->string('photo')->nullable();
            
            // Campos específicos para empresas
            $table->string('company_name')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_description')->nullable();
            $table->string('company_logo')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            
            // Índices para otimização
            $table->index('role');
            $table->index('registration_status');
            $table->index('company_name');
            $table->index('course_completion_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
