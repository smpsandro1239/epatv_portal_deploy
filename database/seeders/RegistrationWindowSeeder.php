<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegistrationWindow;
use Carbon\Carbon;

class RegistrationWindowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Janela ativa atual
        RegistrationWindow::create([
            'start_time' => Carbon::now()->subDays(5),
            'end_time' => Carbon::now()->addDays(10),
            'max_registrations' => 20,
            'current_registrations' => 3,
            'is_active' => true,
        ]);

        // Janela futura (inativa)
        RegistrationWindow::create([
            'start_time' => Carbon::now()->addDays(30),
            'end_time' => Carbon::now()->addDays(40),
            'max_registrations' => 15,
            'current_registrations' => 0,
            'password' => 'epatv2025',
            'is_active' => false,
        ]);

        // Janela passada (inativa)
        RegistrationWindow::create([
            'start_time' => Carbon::now()->subDays(60),
            'end_time' => Carbon::now()->subDays(50),
            'max_registrations' => 10,
            'current_registrations' => 10,
            'is_active' => false,
        ]);
    }
}
