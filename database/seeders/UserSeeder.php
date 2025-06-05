<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar superadmin
        User::create([
            'name' => 'Administrador EPATV',
            'email' => 'admin@epatv.pt',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
            'registration_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Criar empresas de exemplo
        $companies = [
            [
                'name' => 'Empresa ABC',
                'email' => 'empresa@abc.pt',
                'company_name' => 'ABC Tecnologias, Lda',
                'company_city' => 'Braga',
                'company_website' => 'https://www.abc-tech.pt',
                'company_description' => 'Empresa especializada em desenvolvimento de software e soluções tecnológicas.',
                'phone' => '253123456',
            ],
            [
                'name' => 'Empresa XYZ',
                'email' => 'empresa@xyz.pt',
                'company_name' => 'XYZ Indústrias, S.A.',
                'company_city' => 'Vila Verde',
                'company_website' => 'https://www.xyz-industrias.pt',
                'company_description' => 'Empresa industrial com foco em produção e inovação.',
                'phone' => '253654321',
            ],
        ];

        foreach ($companies as $company) {
            User::create([
                'name' => $company['name'],
                'email' => $company['email'],
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'registration_status' => 'approved',
                'email_verified_at' => now(),
                'company_name' => $company['company_name'],
                'company_city' => $company['company_city'],
                'company_website' => $company['company_website'],
                'company_description' => $company['company_description'],
                'phone' => $company['phone'],
            ]);
        }

        // Criar ex-alunos de exemplo
        $students = [
            [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'course_completion_year' => 2023,
                'phone' => '912345678',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'course_completion_year' => 2022,
                'phone' => '923456789',
            ],
            [
                'name' => 'Pedro Costa',
                'email' => 'pedro@example.com',
                'course_completion_year' => 2021,
                'phone' => '934567890',
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana@example.com',
                'course_completion_year' => 2023,
                'phone' => '945678901',
                'registration_status' => 'pending',
            ],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password123'),
                'role' => 'student',
                'registration_status' => $student['registration_status'] ?? 'approved',
                'email_verified_at' => isset($student['registration_status']) ? null : now(),
                'course_completion_year' => $student['course_completion_year'],
                'phone' => $student['phone'],
            ]);
        }
    }
}
