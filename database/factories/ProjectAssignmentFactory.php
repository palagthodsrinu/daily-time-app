<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ProjectAssignment>
 */
class ProjectAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'    => Project::factory(),
            'employee_id'   => User::factory(),
            'supervisor_id' => User::factory(),
            'is_active'     => true,
        ];
    }
}
