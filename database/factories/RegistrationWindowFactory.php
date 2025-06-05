<?php

namespace Database\Factories;

use App\Models\RegistrationWindow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class RegistrationWindowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RegistrationWindow::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_time' => Carbon::now()->subDays(1),
            'end_time' => Carbon::now()->addDays(10),
            'max_registrations' => 20,
            'current_registrations' => 0,
            'is_active' => true,
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
     * Configure the model with password.
     */
    public function withPassword(string $password = 'epatv2025'): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => $password,
        ]);
    }

    /**
     * Configure the model as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => Carbon::now()->subDays(20),
            'end_time' => Carbon::now()->subDays(10),
        ]);
    }

    /**
     * Configure the model as future.
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => Carbon::now()->addDays(10),
            'end_time' => Carbon::now()->addDays(20),
        ]);
    }

    /**
     * Configure the model as full.
     */
    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_registrations' => 10,
            'current_registrations' => 10,
        ]);
    }
}
