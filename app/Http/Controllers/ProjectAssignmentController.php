<?php

namespace App\Http\Controllers;

use App\Models\ProjectAssignment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectAssignmentController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth','role:admin']); // only admin can hit this controller actions
    }
    public function index(Request $request)
{
    $query = ProjectAssignment::with(['project', 'employee', 'supervisor']);
    
    // Search functionality
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('project', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('supervisor', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        });
    }
    
    $assignments = $query->orderByDesc('id')->paginate(10);
    
    return view('assignments.index', compact('assignments'));
}

    public function create()
    {
        $projects = Project::with('client')->orderBy('name')->get();
        // employees = users that have employee role
        $employees = User::whereHas('roles', fn($q)=>$q->where('name','employee'))->get();
        $supervisors = User::whereHas('roles', fn($q)=>$q->where('name','supervisor'))->get();

        return view('assignments.create', compact('projects','employees','supervisors'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'employee_ids' => 'required|array',
        'employee_ids.*' => 'required|exists:users,id',
        'supervisor_id' => 'required|exists:users,id',
        'is_active' => 'nullable|boolean'
    ]);
    
    $data['is_active'] = $request->has('is_active') ? 1 : 0;
    $projectId = $data['project_id'];
    $supervisorId = $data['supervisor_id'];
    $isActive = $data['is_active'];

    $createdCount = 0;
    $updatedCount = 0;
    
    // Loop through each selected employee and create/update their assignment
    foreach ($request->employee_ids as $employeeId) {
        $assignment = ProjectAssignment::updateOrCreate(
            [
                'project_id' => $projectId,
                'employee_id' => $employeeId
            ],
            [
                'supervisor_id' => $supervisorId,
                'is_active' => $isActive
            ]
        );
        
        if ($assignment->wasRecentlyCreated) {
            $createdCount++;
        } else {
            $updatedCount++;
        }
    }

    $message = '';
    if ($createdCount > 0 && $updatedCount > 0) {
        $message = "Assignment created for $createdCount employee(s) and updated for $updatedCount employee(s).";
    } elseif ($createdCount > 0) {
        $message = "Assignment created for $createdCount employee(s).";
    } elseif ($updatedCount > 0) {
        $message = "Assignment updated for $updatedCount employee(s).";
    }

    return redirect()->route('assignments.index')->with('success', $message);
}

    public function edit(ProjectAssignment $assignment)
    {
        $projects = Project::with('client')->orderBy('name')->get();
        $employees = User::whereHas('roles', fn($q)=>$q->where('name','employee'))->get();
        $supervisors = User::whereHas('roles', fn($q)=>$q->where('name','supervisor'))->get();

        return view('assignments.edit', compact('assignment','projects','employees','supervisors'));
    }

    public function update(Request $request, ProjectAssignment $assignment)
    {
        $data = $request->validate([
            'project_id'=>'required|exists:projects,id',
            'employee_id'=>'required|exists:users,id',
            'supervisor_id'=>'required|exists:users,id',
            'is_active'=>'nullable|boolean'
        ]);
        $data['is_active']=$request->has('is_active')?1:0;
        $assignment->update($data);
        return redirect()->route('assignments.index')->with('success','Assignment updated.');
    }

    public function destroy(ProjectAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success','Assignment removed.');
    }
}
