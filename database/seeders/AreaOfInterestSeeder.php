<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AreaOfInterest;

class AreaOfInterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'name' => 'Programação',
                'description' => 'Desenvolvimento de software, aplicações web e mobile.',
            ],
            [
                'name' => 'Design',
                'description' => 'Design gráfico, web design, UI/UX.',
            ],
            [
                'name' => 'Redes',
                'description' => 'Administração de redes, segurança informática.',
            ],
            [
                'name' => 'Multimédia',
                'description' => 'Produção de conteúdos multimédia, audiovisual.',
            ],
            [
                'name' => 'Gestão',
                'description' => 'Gestão de projetos, administração.',
            ],
            [
                'name' => 'Marketing Digital',
                'description' => 'SEO, SEM, redes sociais, marketing de conteúdo.',
            ],
            [
                'name' => 'Suporte Técnico',
                'description' => 'Helpdesk, suporte ao utilizador.',
            ],
            [
                'name' => 'Análise de Dados',
                'description' => 'Business Intelligence, análise estatística.',
            ],
        ];

        foreach ($areas as $area) {
            AreaOfInterest::create([
                'name' => $area['name'],
                'description' => $area['description'],
                'is_active' => true,
            ]);
        }
    }
}
