<?php

namespace Database\Factories;

use App\Models\GroupMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupMember>
 */
class GroupMemberFactory extends Factory
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
            'nim' => (string) fake()->unique()->numerify('########'),
            'class' => fake()->randomElement(['TI-3A', 'TI-3B', 'TI-3C']),
            'role' => fake()->randomElement(['Ketua', 'Anggota']),
        ];
    }
}
