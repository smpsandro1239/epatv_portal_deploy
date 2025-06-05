<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AreaOfInterest;
use App\Models\RegistrationWindow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_during_open_window()
    {
        // Criar áreas de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar janela de registo ativa
        RegistrationWindow::create([
            'start_time' => Carbon::now()->subDays(1),
            'end_time' => Carbon::now()->addDays(10),
            'max_registrations' => 20,
            'current_registrations' => 0,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '912345678',
            'course_completion_year' => 2023,
            'areas_of_interest' => [$area->id],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registo efetuado com sucesso.',
                'user' => [
                    'name' => 'João Silva',
                    'email' => 'joao@example.com',
                    'registration_status' => 'approved',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'role' => 'student',
            'registration_status' => 'approved',
        ]);

        $this->assertDatabaseHas('user_area_of_interest', [
            'user_id' => User::where('email', 'joao@example.com')->first()->id,
            'area_of_interest_id' => $area->id,
        ]);
    }

    public function test_student_registration_is_pending_outside_window()
    {
        // Criar áreas de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar superadmin para receber notificação
        $admin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '923456789',
            'course_completion_year' => 2022,
            'areas_of_interest' => [$area->id],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registo submetido com sucesso e aguarda aprovação.',
                'status' => 'pending',
                'user' => [
                    'name' => 'Maria Santos',
                    'email' => 'maria@example.com',
                    'registration_status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'maria@example.com',
            'role' => 'student',
            'registration_status' => 'pending',
        ]);
    }

    public function test_company_can_register()
    {
        $response = $this->postJson('/api/register/company', [
            'name' => 'Gestor ABC',
            'email' => 'gestor@abc.pt',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '253123456',
            'company_name' => 'ABC Tecnologias, Lda',
            'company_city' => 'Braga',
            'company_website' => 'https://www.abc-tech.pt',
            'company_description' => 'Empresa especializada em desenvolvimento de software.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registo de empresa efetuado com sucesso.',
                'user' => [
                    'name' => 'Gestor ABC',
                    'email' => 'gestor@abc.pt',
                    'company_name' => 'ABC Tecnologias, Lda',
                    'role' => 'admin',
                    'registration_status' => 'approved',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'gestor@abc.pt',
            'role' => 'admin',
            'company_name' => 'ABC Tecnologias, Lda',
            'registration_status' => 'approved',
        ]);
    }

    public function test_registration_with_password_requires_valid_password()
    {
        // Criar janela de registo com password
        RegistrationWindow::create([
            'start_time' => Carbon::now()->subDays(1),
            'end_time' => Carbon::now()->addDays(10),
            'max_registrations' => 20,
            'current_registrations' => 0,
            'password' => 'epatv2025',
            'is_active' => true,
        ]);

        // Criar áreas de interesse
        $area = AreaOfInterest::factory()->create();

        // Tentar registo com password incorreta
        $response = $this->postJson('/api/register', [
            'name' => 'Pedro Costa',
            'email' => 'pedro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '934567890',
            'course_completion_year' => 2021,
            'areas_of_interest' => [$area->id],
            'registration_password' => 'senha_errada',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Password de registo inválida.'
            ]);

        // Tentar registo com password correta
        $response = $this->postJson('/api/register', [
            'name' => 'Pedro Costa',
            'email' => 'pedro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '934567890',
            'course_completion_year' => 2021,
            'areas_of_interest' => [$area->id],
            'registration_password' => 'epatv2025',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registo efetuado com sucesso.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'pedro@example.com',
            'role' => 'student',
            'registration_status' => 'approved',
        ]);
    }
}
