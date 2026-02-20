<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'is_active'   => true,
        ];
    }
}
