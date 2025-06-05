<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Usar Hash::make em vez de hash pré-gerado
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'role' => 'student',
            'registration_status' => 'approved',
            'course_completion_year' => fake()->numberBetween(2010, 2025),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model as a company.
     */
    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'registration_status' => 'approved',
            'company_name' => fake()->company(),
            'company_city' => fake()->city(),
            'company_website' => fake()->url(),
            'company_description' => fake()->paragraph(),
        ]);
    }

    /**
     * Configure the model as a superadmin.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'registration_status' => 'approved',
        ]);
    }

    /**
     * Configure the model with pending registration status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'registration_status' => 'pending',
        ]);
    }

    /**
     * Configure the model with rejected registration status.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'registration_status' => 'rejected',
        ]);
    }
}
