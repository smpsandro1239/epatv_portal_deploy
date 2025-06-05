<?php

namespace Database\Factories;

use App\Models\AreaOfInterest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaOfInterestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AreaOfInterest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Desenvolvimento Web',
                'Programação',
                'Design Gráfico',
                'Marketing Digital',
                'Redes e Sistemas',
                'Multimédia',
                'Gestão de Projetos',
                'Administração de Sistemas',
                'Cibersegurança',
                'Inteligência Artificial',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
