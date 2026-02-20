<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'doj',
        'is_active',
        'employee_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'doj' => 'date',
        'is_active' => 'boolean',
    ];

    // roles many-to-many
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

   public function hasRole($roles)
{
    if (!$this->relationLoaded('roles')) {
        $this->load('roles');
    }

    $roleNames = $this->roles->pluck('name')->map(fn($r) => strtolower($r))->toArray();

    // If $roles is a string, convert it to array
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    // Convert all roles to lowercase for comparison
    $roles = array_map('strtolower', $roles);

    // Check if user has any of the required roles
    foreach ($roles as $role) {
        if (in_array($role, $roleNames)) {
            return true;
        }
    }

    return false;
}

    public function isAdmin() { return $this->hasRole('admin'); }
    public function isSupervisor() { return $this->hasRole('supervisor'); }
    public function isEmployee() { return $this->hasRole('employee'); }

    public function employeeAssignments()
    {
        return $this->hasMany(ProjectAssignment::class, 'employee_id');
    }

   public function supervisorAssignments()
    {
        return $this->hasMany(ProjectAssignment::class, 'supervisor_id');
    }


    public function timesheetEntries()
    {
        return $this->hasMany(TimesheetEntry::class, 'user_id');
    }

    
}
