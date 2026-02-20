<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimesheetEntry;
use App\Models\ProjectAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('supervisor')) {
            return $this->supervisorDashboard();
        } else {
            return $this->employeeDashboard();
        }
    }

    private function adminDashboard()
    {
        $totalUsers = User::count();
        $totalClients = Client::count();
        $totalProjects = Project::where('is_active', true)->count();
        $pendingTimesheets = TimesheetEntry::where('status', 'pending')->count();
        
        // Recent timesheets (all)
        $recentTimesheets = TimesheetEntry::with(['user', 'project.client'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Additional admin metrics
        $activeUsers = User::where('is_active', true)->count();
        $totalHoursThisMonth = TimesheetEntry::where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->sum('hours');
        $recentRegistrations = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalClients', 
            'totalProjects',
            'pendingTimesheets',
            'recentTimesheets',
            'activeUsers',
            'totalHoursThisMonth',
            'recentRegistrations'
        ));
    }

    private function supervisorDashboard()
    {
        $user = Auth::user();
        
        // Get team members (employees assigned to supervisor's projects)
        $teamMembers = User::whereHas('employeeAssignments', function($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })->count();

        // Get supervised projects count
        $supervisedProjects = Project::whereHas('assignments', function($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })->where('is_active', true)->count();

        // Pending timesheets for supervisor's team
        $pendingTimesheets = TimesheetEntry::where('status', 'pending')
            ->whereExists(function($query) use ($user) {
                $query->from('project_assignments')
                    ->whereColumn('project_assignments.project_id', 'timesheet_entries.project_id')
                    ->whereColumn('project_assignments.employee_id', 'timesheet_entries.user_id')
                    ->where('project_assignments.supervisor_id', $user->id);
            })->count();

        // Team hours this week
        $teamHoursThisWeek = TimesheetEntry::where('status', 'approved')
            ->whereExists(function($query) use ($user) {
                $query->from('project_assignments')
                    ->whereColumn('project_assignments.project_id', 'timesheet_entries.project_id')
                    ->whereColumn('project_assignments.employee_id', 'timesheet_entries.user_id')
                    ->where('project_assignments.supervisor_id', $user->id);
            })
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours');

        // Recent team timesheets
        $recentTimesheets = TimesheetEntry::with(['user', 'project.client'])
            ->whereExists(function($query) use ($user) {
                $query->from('project_assignments')
                    ->whereColumn('project_assignments.project_id', 'timesheet_entries.project_id')
                    ->whereColumn('project_assignments.employee_id', 'timesheet_entries.user_id')
                    ->where('project_assignments.supervisor_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Urgent approvals (pending for more than 2 days)
        $urgentApprovals = TimesheetEntry::where('status', 'pending')
            ->whereExists(function($query) use ($user) {
                $query->from('project_assignments')
                    ->whereColumn('project_assignments.project_id', 'timesheet_entries.project_id')
                    ->whereColumn('project_assignments.employee_id', 'timesheet_entries.user_id')
                    ->where('project_assignments.supervisor_id', $user->id);
            })
            ->where('created_at', '<=', now()->subDays(2))
            ->count();

        // Set employee-specific variables to null to avoid undefined variable errors
        $myProjects = null;
        $weekHours = null;
        $myPendingTimesheets = null;
        $draftEntries = null;
        $monthlyHours = null;

        return view('dashboard.index', compact(
            'teamMembers',
            'supervisedProjects',
            'pendingTimesheets',
            'teamHoursThisWeek',
            'recentTimesheets',
            'urgentApprovals',
            'myProjects',
            'weekHours',
            'myPendingTimesheets',
            'draftEntries',
            'monthlyHours'
        ));
    }

    private function employeeDashboard()
    {
        $user = Auth::user();
        
        // Employee's active projects
        $myProjects = ProjectAssignment::where('employee_id', $user->id)
            ->whereHas('project', function($query) {
                $query->where('is_active', true);
            })->count();

        // This week's hours
        $weekHours = TimesheetEntry::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours');

        // Pending approvals
        $myPendingTimesheets = TimesheetEntry::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Draft entries
        $draftEntries = TimesheetEntry::where('user_id', $user->id)
            ->where('status', 'draft')
            ->count();

        // Recent personal timesheets
        $recentTimesheets = TimesheetEntry::with(['user', 'project.client'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Monthly total hours
        $monthlyHours = TimesheetEntry::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->sum('hours');

        // Set supervisor-specific variables to null to avoid undefined variable errors
        $teamMembers = null;
        $supervisedProjects = null;
        $teamHoursThisWeek = null;
        $urgentApprovals = null;

        return view('dashboard.index', compact(
            'myProjects',
            'weekHours',
            'myPendingTimesheets',
            'draftEntries',
            'recentTimesheets',
            'monthlyHours',
            'teamMembers',
            'supervisedProjects',
            'teamHoursThisWeek',
            'urgentApprovals'
        ));
    }
}