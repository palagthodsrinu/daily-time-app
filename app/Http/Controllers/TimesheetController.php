<?php

namespace App\Http\Controllers;

use App\Models\TimesheetEntry;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   
    public function index()
    {
        $user = Auth::user();
        
        // Get user's timesheet entries with pagination
        $entries = TimesheetEntry::where('user_id', $user->id)
            ->with(['project.client'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Get only projects assigned to the current user
        $projects = Project::whereHas('assignments', function($query) use ($user) {
                $query->where('employee_id', $user->id);
            })
            ->where('is_active', true)
            ->with('client')
            ->orderBy('name')
            ->get();

        // Calculate stats for dashboard cards
        $draftCount = TimesheetEntry::where('user_id', $user->id)->where('status', 'draft')->count();
        $pendingCount = TimesheetEntry::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedCount = TimesheetEntry::where('user_id', $user->id)->where('status', 'approved')->count();
        
        $totalHours = TimesheetEntry::where('user_id', $user->id)->sum('hours');
        $totalMinutes = TimesheetEntry::where('user_id', $user->id)->sum('minutes');

        return view('timesheets.index', compact(
            'entries', 
            'projects',
            'draftCount', 
            'pendingCount', 
            'approvedCount',
            'totalHours',
            'totalMinutes'
        ));
    }

    public function store(Request $request)
{
    $user = Auth::user();

    $data = $request->validate([
        'date' => [
            'required',
            'date',
            'before_or_equal:today'
        ],
        'project_ids' => [
            'required',
            'array',
            'min:1'
        ],
        'project_ids.*' => [
            'exists:projects,id',
            function ($attribute, $value, $fail) use ($user) {
                $isAssigned = ProjectAssignment::where('project_id', $value)
                    ->where('employee_id', $user->id)
                    ->exists();

                if (!$isAssigned) {
                    $fail('One or more selected projects are not assigned to you.');
                }
            }
        ],
        'hours' => 'required|integer|min:0|max:24',
        'minutes' => 'required|integer|in:0,15,30,45',
        'description' => 'required|string|max:500',
        'status' => 'required|in:draft,pending'
    ]);

    if ($data['hours'] == 0 && $data['minutes'] == 0) {
        return redirect()->back()->withErrors(['hours' => 'Total time must be greater than 0']);
    }

    // CREATE MULTIPLE TIMESHEET ENTRIES
    foreach ($request->project_ids as $projectId) {
        TimesheetEntry::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'date' => $data['date'],
            'hours' => $data['hours'],
            'minutes' => $data['minutes'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);
    }

    $message = $data['status'] === 'pending' 
        ? 'Timesheet submitted for approval successfully!' 
        : 'Timesheet saved as draft successfully!';

    return redirect()->route('timesheets.index')->with('success', $message);
}


    public function edit($id)
    {
        $user = Auth::user();
        $timesheet = TimesheetEntry::findOrFail($id);
        
        // Check if user owns the timesheet or is admin
        if ($timesheet->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'This action is unauthorized.');
        }
        
        // Only allow editing of draft or rejected entries
        if (!in_array($timesheet->status, ['draft', 'rejected'])) {
            return redirect()->route('timesheets.index')->with('error', 'Only draft or rejected entries can be edited.');
        }
        
        // Get only projects assigned to the current user
        $projects = Project::whereHas('assignments', function($query) use ($user) {
                $query->where('employee_id', $user->id);
            })
            ->where('is_active', true)
            ->with('client')
            ->orderBy('name')
            ->get();

        return view('timesheets.edit', compact('timesheet', 'projects'));
    }

    public function update(Request $request, TimesheetEntry $timesheet)
    {
        $user = Auth::user();
        
        // Check if user owns the timesheet or is admin
        if ($timesheet->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'This action is unauthorized.');
        }
        
        // Only allow updating of draft or rejected entries
        if (!in_array($timesheet->status, ['draft', 'rejected'])) {
            return redirect()->route('timesheets.index')->with('error', 'Only draft or rejected entries can be updated.');
        }
        
        $data = $request->validate([
            'date' => [
                'required', 
                'date',
                'before_or_equal:today'
            ],
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attribute, $value, $fail) use ($user) {
                    // Check if project is assigned to the user
                    $isAssigned = ProjectAssignment::where('project_id', $value)
                        ->where('employee_id', $user->id)
                        ->exists();
                    
                    if (!$isAssigned) {
                        $fail('The selected project is not assigned to you.');
                    }
                }
            ],
            'hours' => 'required|integer|min:0|max:24',
            'minutes' => 'required|integer|in:0,15,30,45',
            'description' => 'required|string|max:500', // Changed to required
            'status' => 'required|in:draft,pending'
        ], [
            'date.required' => 'Date is required',
            'date.date' => 'Please enter a valid date',
            'date.before_or_equal' => 'Date cannot be in the future',
            'project_id.required' => 'Please select a project',
            'project_id.exists' => 'The selected project does not exist',
            'hours.required' => 'Hours are required',
            'hours.min' => 'Hours cannot be negative',
            'hours.max' => 'Hours cannot exceed 24',
            'minutes.required' => 'Minutes are required',
            'minutes.in' => 'Please select valid minutes (0, 15, 30, or 45)',
            'description.required' => 'Description is required', // New error message
            'description.max' => 'Description cannot exceed 500 characters',
            'status.required' => 'Status is required',
        ]);
        
        // Check if total time is greater than 0
        if ($data['hours'] == 0 && $data['minutes'] == 0) {
            return redirect()->back()->withErrors(['hours' => 'Total time must be greater than 0']);
        }
        
        $timesheet->update($data);
        
        $message = $data['status'] === 'pending' 
            ? 'Timesheet submitted for approval successfully!' 
            : 'Timesheet updated successfully!';
            
        return redirect()->route('timesheets.index')->with('success', $message);
    }

    public function destroy(TimesheetEntry $timesheet)
    {
        $user = Auth::user();
        
        // Check if user owns the timesheet or is admin
        if ($timesheet->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'This action is unauthorized.');
        }
        
        // Only allow deletion of draft or rejected entries
        if (!in_array($timesheet->status, ['draft', 'rejected'])) {
            return redirect()->route('timesheets.index')->with('error', 'Only draft or rejected entries can be deleted.');
        }
        
        $timesheet->delete();
        
        return redirect()->route('timesheets.index')->with('success', 'Timesheet entry deleted successfully.');
    }
}