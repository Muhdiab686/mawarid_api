<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\SubAgency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubAgency>
 */
class SubAgencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->unique()->words(3, true),
        ];
    }
}
