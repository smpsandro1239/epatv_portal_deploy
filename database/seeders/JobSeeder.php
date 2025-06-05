<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jobs')->insert([
            [
                'company_id' => 1, // Ensure a user with this ID exists
                'category_id' => 1, // Matches an area_of_interests ID
                'title' => 'Desenvolvedor Web',
                'description' => 'Desenvolvimento de aplicações web.',
                'location' => 'Lisboa',
                'salary' => 30000.00,
                'contract_type' => 'full-time',
                'expiration_date' => Carbon::now()->addMonths(1),
                'is_active' => true,
                'views_count' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more job records as needed
        ]);
    }
}
