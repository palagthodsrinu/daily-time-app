<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\TimesheetEntry;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------
        // 1. Create roles
        // ---------------------------------------------
        $adminRole = Role::create(['name' => 'admin']);
        $supervisorRole = Role::create(['name' => 'supervisor']);
        $employeeRole = Role::create(['name' => 'employee']);

        // ---------------------------------------------
        // 2. Create Admin
        // ---------------------------------------------
        $admin = User::create([
            'first_name' => 'Site',
            'last_name'  => 'Admin',
            'email'      => 'admin@example.com',
            'password'   => Hash::make('password'),
            'doj'        => now()->subYears(2),
            'is_active'  => true,
        ]);
        $admin->roles()->attach($adminRole->id);

        // ---------------------------------------------
        // 3. Create Supervisor
        // ---------------------------------------------
        $supervisor = User::create([
            'first_name' => 'John',
            'last_name'  => 'Supervisor',
            'email'      => 'supervisor@example.com',
            'password'   => Hash::make('password'),
            'doj'        => now()->subYear(),
            'is_active'  => true,
        ]);
        $supervisor->roles()->attach($supervisorRole->id);

        // ---------------------------------------------
        // 4. Create Employees
        // ---------------------------------------------
        $employee1 = User::create([
            'first_name' => 'Alice',
            'last_name'  => 'Employee',
            'email'      => 'alice@example.com',
            'password'   => Hash::make('password'),
            'doj'        => now()->subMonths(8),
            'is_active'  => true,
        ]);
        $employee1->roles()->attach($employeeRole->id);

        $employee2 = User::create([
            'first_name' => 'Bob',
            'last_name'  => 'Developer',
            'email'      => 'bob@example.com',
            'password'   => Hash::make('password'),
            'doj'        => now()->subMonths(4),
            'is_active'  => true,
        ]);
        $employee2->roles()->attach($employeeRole->id);

        // ---------------------------------------------
        // 5. Create Clients & Projects
        // ---------------------------------------------
        $client = Client::create([
            'name'        => 'Acme Corporation',
            'description' => 'Top priority client',
            'is_active'   => true,
        ]);

        $projectA = Project::create([
            'client_id'   => $client->id,
            'name'        => 'Website Revamp',
            'description' => 'Rebuild entire frontend',
            'is_active'   => true,
        ]);

        $projectB = Project::create([
            'client_id'   => $client->id,
            'name'        => 'Mobile App',
            'description' => 'iOS + Android app',
            'is_active'   => true,
        ]);

        // ---------------------------------------------
        // 6. Assign employees to projects (supervisor ↔ employee)
        // ---------------------------------------------
        ProjectAssignment::create([
            'project_id'    => $projectA->id,
            'employee_id'   => $employee1->id,
            'supervisor_id' => $supervisor->id,
            'is_active'     => true,
        ]);

        ProjectAssignment::create([
            'project_id'    => $projectB->id,
            'employee_id'   => $employee2->id,
            'supervisor_id' => $supervisor->id,
            'is_active'     => true,
        ]);

        // ---------------------------------------------
        // 7. Add Timesheet entries
        // ---------------------------------------------
        TimesheetEntry::create([
            'user_id'    => $employee1->id,
            'project_id' => $projectA->id,
            'date'       => today()->subDays(2),
            'hours'      => 4,
            'minutes'    => 30,
            'description'=> 'UI redesign work',
            'status'     => 'approved',
            'approved_by'=> $supervisor->id,
            'approved_at'=> now(),
        ]);

        TimesheetEntry::create([
            'user_id'    => $employee2->id,
            'project_id' => $projectB->id,
            'date'       => today()->subDay(),
            'hours'      => 5,
            'minutes'    => 0,
            'description'=> 'API development',
            'status'     => 'pending'
        ]);
    }
}
