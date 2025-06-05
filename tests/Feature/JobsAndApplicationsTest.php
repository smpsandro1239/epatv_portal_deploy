<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\AreaOfInterest;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class JobsAndApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_create_job()
    {
        // Criar área de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar empresa
        $company = User::factory()->create([
            'role' => 'admin',
            'registration_status' => 'approved',
            'company_name' => 'Empresa Teste',
        ]);

        $token = $company->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/jobs', [
            'title' => 'Desenvolvedor Web',
            'description' => 'Vaga para desenvolvedor web com experiência em Laravel e Vue.js',
            'location' => 'Braga',
            'salary' => 1500,
            'contract_type' => 'full-time',
            'expiration_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'category_id' => $area->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Oferta de emprego criada com sucesso.',
                'job' => [
                    'title' => 'Desenvolvedor Web',
                    'location' => 'Braga',
                    'contract_type' => 'full-time',
                    'company_id' => $company->id,
                ]
            ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Desenvolvedor Web',
            'company_id' => $company->id,
            'is_active' => 1,
        ]);
    }

    public function test_student_can_apply_to_job()
    {
        // Criar área de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar empresa
        $company = User::factory()->create([
            'role' => 'admin',
            'registration_status' => 'approved',
            'company_name' => 'Empresa Teste',
        ]);

        // Criar oferta
        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'Desenvolvedor Web',
            'description' => 'Vaga para desenvolvedor web com experiência em Laravel e Vue.js',
            'location' => 'Braga',
            'salary' => 1500,
            'contract_type' => 'full-time',
            'expiration_date' => Carbon::now()->addDays(30),
            'category_id' => $area->id,
            'is_active' => true,
        ]);

        // Criar ex-aluno
        $student = User::factory()->create([
            'role' => 'student',
            'registration_status' => 'approved',
            'course_completion_year' => 2023,
            'cv' => 'cvs/exemplo.pdf',
        ]);

        $token = $student->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/jobs/' . $job->id . '/apply', [
            'message' => 'Estou interessado nesta vaga e acredito que meu perfil é adequado.',
            'use_profile_cv' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Candidatura submetida com sucesso.',
                'application' => [
                    'job_id' => $job->id,
                    'user_id' => $student->id,
                    'status' => 'pending',
                ]
            ]);

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'user_id' => $student->id,
            'status' => 'pending',
        ]);
    }

    public function test_company_can_update_application_status()
    {
        // Criar área de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar empresa
        $company = User::factory()->create([
            'role' => 'admin',
            'registration_status' => 'approved',
            'company_name' => 'Empresa Teste',
        ]);

        // Criar oferta
        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'Desenvolvedor Web',
            'description' => 'Vaga para desenvolvedor web com experiência em Laravel e Vue.js',
            'location' => 'Braga',
            'salary' => 1500,
            'contract_type' => 'full-time',
            'expiration_date' => Carbon::now()->addDays(30),
            'category_id' => $area->id,
            'is_active' => true,
        ]);

        // Criar ex-aluno
        $student = User::factory()->create([
            'role' => 'student',
            'registration_status' => 'approved',
            'course_completion_year' => 2023,
        ]);

        // Criar candidatura
        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_id' => $job->id,
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $student->phone,
            'course_completion_year' => $student->course_completion_year,
            'cv' => 'cvs/exemplo.pdf',
            'status' => 'pending',
        ]);

        $token = $company->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/applications/' . $application->id . '/status', [
            'status' => 'accepted',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Estado da candidatura atualizado com sucesso.',
                'application' => [
                    'status' => 'accepted',
                ]
            ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'accepted',
            'processed_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function test_student_can_save_job()
    {
        // Criar área de interesse
        $area = AreaOfInterest::factory()->create();
        
        // Criar empresa
        $company = User::factory()->create([
            'role' => 'admin',
            'registration_status' => 'approved',
            'company_name' => 'Empresa Teste',
        ]);

        // Criar oferta
        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'Desenvolvedor Web',
            'description' => 'Vaga para desenvolvedor web com experiência em Laravel e Vue.js',
            'location' => 'Braga',
            'salary' => 1500,
            'contract_type' => 'full-time',
            'expiration_date' => Carbon::now()->addDays(30),
            'category_id' => $area->id,
            'is_active' => true,
        ]);

        // Criar ex-aluno
        $student = User::factory()->create([
            'role' => 'student',
            'registration_status' => 'approved',
        ]);

        $token = $student->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/jobs/' . $job->id . '/save');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Oferta guardada com sucesso.',
            ]);

        $this->assertDatabaseHas('saved_jobs', [
            'job_id' => $job->id,
            'user_id' => $student->id,
        ]);
    }
}
