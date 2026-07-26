<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return array(
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'github_id' => fake()->unique()->randomNumber(8),
            'avatar' => fake()->imageUrl(),
            'github_token' => Str::random(40),
            'github_refresh_token' => Str::random(40),
            'remember_token' => Str::random(10),
        );
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static {
        return $this->state(fn (array $attributes) => array(
            'email_verified_at' => null,
        ));
    }
}
