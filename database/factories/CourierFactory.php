<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'level' => fake()->numberBetween(1, 5),
            'is_active' => true,
            'registered_at' => now(),
        ];
    }

    public function level(int $level): static
    {
        return $this->state([
            'level' => $level,
        ]);
    }
}
