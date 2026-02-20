<?php

namespace App\Http\Controllers;

use App\Models\TimesheetEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF; // for barryvdh/laravel-dompdf

class SupervisorTimesheetController extends Controller
{
    public function index(Request $request)
    {
        // Only supervisors (or admin) should access; simple check:
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) {
            abort(403);
        }

        // filter by date range optional
        $query = TimesheetEntry::with(['employee','project','approver']);

        // Find entries for employees supervised by this user
        // join project_assignments to filter by supervisor
        $query->whereExists(function($q) use ($user){
            $q->from('project_assignments')
              ->whereColumn('project_assignments.project_id','timesheet_entries.project_id')
              ->whereColumn('project_assignments.employee_id','timesheet_entries.user_id')
              ->where('project_assignments.supervisor_id', $user->id);
        });

        if ($request->filled('from')) {
            $query->whereDate('date','>=',$request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date','<=',$request->to);
        }

        // Filter by status (pending, approved, rejected, or all)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $entries = $query->orderByDesc('date')->paginate(15)->withQueryString();
        return view('supervisor.timesheets.index', compact('entries'));
    }

    public function approve(TimesheetEntry $timesheet)
    {
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) abort(403);

        $timesheet->update([
            'status'=>'approved',
            'approved_by'=> $user->id,
            'approved_at'=> now(),
        ]);

        return back()->with('success','Timesheet approved.');
    }

    public function bulkApprove(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) abort(403);

        $timesheetIds = $request->input('timesheet_ids', []);
        
        if (empty($timesheetIds)) {
            return back()->with('error', 'No timesheets selected.');
        }

        TimesheetEntry::whereIn('id', $timesheetIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

        return back()->with('success', count($timesheetIds) . ' timesheets approved successfully.');
    }

    public function reject(TimesheetEntry $timesheet)
    {
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) abort(403);

        $timesheet->update([
            'status'=>'rejected',
            'approved_by'=> $user->id,
            'approved_at'=> now(),
        ]);

        return back()->with('success','Timesheet rejected.');
    }

    public function bulkReject(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) abort(403);

        $timesheetIds = $request->input('timesheet_ids', []);
        
        if (empty($timesheetIds)) {
            return back()->with('error', 'No timesheets selected.');
        }

        TimesheetEntry::whereIn('id', $timesheetIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

        return back()->with('success', count($timesheetIds) . ' timesheets rejected successfully.');
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('supervisor') && ! $user->hasRole('admin')) abort(403);

        // get same query as index
        $query = TimesheetEntry::with(['employee','project','approver']);
        $query->whereExists(function($q) use ($user){
            $q->from('project_assignments')
              ->whereColumn('project_assignments.project_id','timesheet_entries.project_id')
              ->whereColumn('project_assignments.employee_id','timesheet_entries.user_id')
              ->where('project_assignments.supervisor_id', $user->id);
        });

        if ($request->filled('from')) $query->whereDate('date','>=',$request->from);
        if ($request->filled('to')) $query->whereDate('date','<=',$request->to);
        
        // Filter by status for PDF export too
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $entries = $query->orderByDesc('date')->get();

        $pdf = PDF::loadView('supervisor.timesheets.pdf', compact('entries','user'));
        return $pdf->download('timesheets_export_'.now()->format('Ymd_His').'.pdf');
    }
}