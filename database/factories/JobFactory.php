<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\AreaOfInterest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => User::factory()->company(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'location' => $this->faker->city(),
            'salary' => $this->faker->numberBetween(800, 3000),
            'contract_type' => $this->faker->randomElement(['full-time', 'part-time', 'internship', 'temporary', 'freelance']),
            'expiration_date' => Carbon::now()->addDays(30),
            'category_id' => AreaOfInterest::factory(),
            'is_active' => true,
            'views_count' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the model as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the model as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiration_date' => Carbon::now()->subDays(5),
        ]);
    }
}
