<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id'   => Client::factory(),
            'name'        => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'is_active'   => true,
        ];
    }
}
