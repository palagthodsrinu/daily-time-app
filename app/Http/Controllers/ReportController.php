<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TimesheetEntry;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProjectAssignment;

class ReportController extends Controller
{
   public function index(Request $request)
{
    // Supervisors selected
    $selectedSupervisors = is_array($request->supervisors) ? $request->supervisors : [];
    $selectedEmployees   = is_array($request->employees) ? $request->employees : [];

    // Calculate allSelectedUsers here
    $allSelectedUsers = array_merge($selectedEmployees, $selectedSupervisors);

    /**
     * EMPLOYEES LIST FILTER LOGIC
     * ----------------------------------
     * If supervisors selected → show ONLY employees belonging to them
     * Else → show all employees
     */
    if (!empty($selectedSupervisors)) {
        $employeesQuery = User::whereHas('employeeAssignments', function ($q) use ($selectedSupervisors) {
            $q->whereIn('supervisor_id', $selectedSupervisors);
        });
    } else {
        $employeesQuery = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        });
    }

    $employees = $employeesQuery
                    ->orderBy('first_name')
                    ->paginate(20, ['*'], 'employees_page')
                    ->withQueryString();

    /**
     * SUPERVISORS LIST
     */
    $supervisors = User::whereHas('roles', function ($q) {
                            $q->where('name', 'supervisor');
                        })
                        ->orderBy('first_name')
                        ->paginate(20, ['*'], 'supervisors_page')
                        ->withQueryString();

    /**
     * TIMESHEET QUERY
     */
    $query = TimesheetEntry::with(['user', 'project']);
    
    // Add employee_type filter
    if ($request->has('employee_type') && $request->employee_type) {
        $query->whereHas('user', function ($userQuery) use ($request) {
            $userQuery->where('employee_type', $request->employee_type);
        });
    }

    // Apply common filters
    $this->applyFilters($query, $request);

    // Order by latest
    $entries = $query->orderByDesc('date')
                     ->orderByDesc('id')
                     ->paginate(10)
                     ->withQueryString();

    return view('reports.index', compact(
        'employees',
        'supervisors',
        'entries',
        'selectedEmployees',
        'selectedSupervisors',
        'allSelectedUsers' // Now this variable is defined
    ));
}

public function exportPdf(Request $request)
{
$query = TimesheetEntry::with(['user', 'project.client']);

$this->applyFilters($query, $request);

$entries = $query->orderByDesc('date')
    ->orderByDesc('id')
    ->get();
    

$pdf = Pdf::loadView('reports.pdf', [
'entries' => $entries,
'from' => $request->from_date,
'to' => $request->to_date,
]);

return $pdf->download('timesheet-report-' . now()->format('Y-m-d') . '.pdf');
}

    /**
     * Apply filters used by both index+export
     */
    /**
 * Apply filters used by both index+export
 */
protected function applyFilters($query, Request $request): void
{
    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            // project name
            $q->whereHas('project', function ($p) use ($search) {
                $p->where('name', 'LIKE', "%{$search}%");
            })
            // user name, email, role
            ->orWhereHas('user', function ($u) use ($search) {
                $u->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('roles', function ($r) use ($search) {
                      $r->where('name', 'LIKE', "%{$search}%");
                  });
            })
            // status
            ->orWhere('status', 'LIKE', "%{$search}%")
            // description
            ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    // Employee + Supervisor IDs
    $employeeIds   = is_array($request->employees) ? $request->employees : [];
    $supervisorIds = is_array($request->supervisors) ? $request->supervisors : [];

    // Get employees under selected supervisors
    $supervisorEmployeeIds = [];
    if (!empty($supervisorIds) && empty($employeeIds)) {
        // Only supervisors selected, no employees - get all their employees
        $supervisorEmployeeIds = User::whereHas('employeeAssignments', function($q) use ($supervisorIds) {
            $q->whereIn('supervisor_id', $supervisorIds);
        })->pluck('id')->toArray();
    }

    // Merge all user IDs to filter
    $selectedIds = array_merge($employeeIds, $supervisorEmployeeIds, $supervisorIds);
    
    // Remove duplicates and limit to 5
    $selectedIds = array_slice(array_unique($selectedIds), 0, 5);

    if (!empty($selectedIds)) {
        $query->whereIn('user_id', $selectedIds);
    }

    // Employee Type filter
    if ($request->filled('employee_type') && $request->employee_type) {
        $query->whereHas('user', function ($userQuery) use ($request) {
            $userQuery->where('employee_type', $request->employee_type);
        });
    }

    // Date Range
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }
}

/**
 * AJAX user search for typeahead
 * Query params:
 *  - q (string) search term
 *  - role (string) 'supervisor' or 'employee'
 *  - supervisors[] (array) optional supervisor ids to limit employees
 */
// In ReportController.php - update the ajaxUsers method
public function ajaxUsers(Request $request)
{
    \Log::info('AJAX Users Request:', $request->all());
    
    $q = trim($request->query('q', ''));
    $role = $request->query('role', 'employee');
    $supervisors = $request->query('supervisors', []);

    // normalize supervisors to array of ints
    if (is_string($supervisors) && strlen($supervisors) > 0) {
        $supervisors = explode(',', $supervisors);
    }
    $supervisors = array_filter(array_map('intval', (array)$supervisors));

    \Log::info("Searching for: q={$q}, role={$role}, supervisors=" . json_encode($supervisors));

    $usersQuery = User::query()
        ->select(['id', 'first_name', 'last_name', 'email'])
        ->whereHas('roles', function($r) use ($role) {
            $r->where('name', $role);
        });

    // if searching for employees and supervisors provided, filter employees assigned to those supervisors
    if ($role === 'employee' && !empty($supervisors)) {
        $usersQuery->whereHas('employeeAssignments', function($q) use ($supervisors) {
            $q->whereIn('supervisor_id', $supervisors);
        });
    }

    if ($q !== '') {
        $usersQuery->where(function($w) use ($q) {
            $w->where('first_name', 'LIKE', "%{$q}%")
              ->orWhere('last_name', 'LIKE', "%{$q}%")
              ->orWhere('email', 'LIKE', "%{$q}%");
        });
    }

    $results = $usersQuery->orderBy('first_name')->limit(12)->get();

    \Log::info("Found {$results->count()} users");

    // transform to a small payload
    $payload = $results->map(function($u) {
        return [
            'id' => $u->id,
            'text' => trim($u->first_name . ' ' . $u->last_name),
            'email' => $u->email,
        ];
    });

    return response()->json($payload);
}
public function searchSupervisors(Request $request)
{
    $query = User::whereHas('roles', function($q) {
        $q->where('name', 'supervisor');
    });
    
    if ($request->has('q') && !empty($request->q)) {
        $query->where(function($q) use ($request) {
            $q->where('first_name', 'like', '%' . $request->q . '%')
              ->orWhere('last_name', 'like', '%' . $request->q . '%')
              ->orWhere('email', 'like', '%' . $request->q . '%');
        });
    }
    
    // Add employee_type filter if needed
    if ($request->has('employee_type') && $request->employee_type) {
        $query->where('employee_type', $request->employee_type);
    }
    
    $supervisors = $query->limit(10)->get();
    
    return response()->json($supervisors->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'employee_type' => $user->employee_type
        ];
    }));
}

public function searchEmployees(Request $request)
{
    $term = (string) $request->query('q', '');
    $supervisorIds = $request->query('supervisors', []);

    // Convert string to array if needed
    if (is_string($supervisorIds)) {
        $supervisorIds = explode(',', $supervisorIds);
    }
    $supervisorIds = array_filter(array_map('intval', (array)$supervisorIds));

    $query = User::query()
        ->select(['id', 'first_name', 'last_name', 'email'])
        ->whereHas('roles', function($q) {
            $q->where('name', 'employee');
        });

    // Filter by supervisors if provided
    if (!empty($supervisorIds)) {
        $query->whereHas('employeeAssignments', function($q) use ($supervisorIds) {
            $q->whereIn('supervisor_id', $supervisorIds);
        });
    }

    if ($term !== '') {
        $query->where(function ($w) use ($term) {
            $w->where('first_name', 'LIKE', "%{$term}%")
              ->orWhere('last_name', 'LIKE', "%{$term}%")
              ->orWhere('email', 'LIKE', "%{$term}%");
        });
    }

    $results = $query->orderBy('first_name')->limit(20)->get();

    return response()->json($results->map(function($u){
        return [
            'id' => $u->id,
            'name' => trim($u->first_name . ' ' . $u->last_name),
            'email' => $u->email,
        ];
    }));
}


}
