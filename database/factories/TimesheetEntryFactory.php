<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TimesheetEntry>
 */
class TimesheetEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'project_id' => Project::factory(),
            'date'       => $this->faker->date(),
            'hours'      => $this->faker->numberBetween(1, 8),
            'minutes'    => $this->faker->randomElement([0, 15, 30, 45]),
            'description'=> $this->faker->sentence(),
            'status'     => 'draft',
            'approved_by'=> null,
            'approved_at'=> null,
        ];
    }
}
