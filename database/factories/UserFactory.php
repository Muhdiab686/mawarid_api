<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role_type' => UserRole::DashboardViewer,
            'is_active' => true,
        ];
    }

    /**
     * Give the user the given role.
     */
    public function role(UserRole $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role_type' => $role,
        ]);
    }

    /**
     * Give the user full access (senior administration).
     */
    public function seniorAdmin(): static
    {
        return $this->role(UserRole::SeniorAdmin);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
