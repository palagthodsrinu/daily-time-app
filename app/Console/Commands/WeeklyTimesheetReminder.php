<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TimesheetEntry;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WeeklyTimesheetReminder extends Command
{
    protected $signature = 'timesheets:weekly-reminder';
    protected $description = 'Send weekly reminder email based on working hours and employee type';

    public function handle()
    {
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek(); // Monday
        $endOfLastWeek   = Carbon::now()->subWeek()->endOfWeek();   // Sunday

        $this->info("Checking hours from $startOfLastWeek to $endOfLastWeek");

        // Fetch only active employees
        $employees = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })
        ->where('is_active', true)
        ->whereIn('employee_type', ['fulltime', 'contract'])
        ->get();


        foreach ($employees as $employee) {

            // Determine required hours based on employee_type
            $requiredHours = $this->getRequiredHours($employee->employee_type);

            // Calculate weekly working hours
            $entries = TimesheetEntry::where('user_id', $employee->id)
                ->whereBetween('date', [$startOfLastWeek, $endOfLastWeek])
                ->get();

            $totalHours = 0;

            foreach ($entries as $entry) {
                $totalHours += $entry->hours + ($entry->minutes / 60);
            }

            // If employee did not meet the required hours → send reminder
            if ($totalHours < $requiredHours) {

                $this->info("Sending reminder to {$employee->email} - Hours logged: $totalHours / $requiredHours");

                Mail::to($employee->email)
                    ->send(new \App\Mail\WeeklyTimesheetReminderMail($employee, $totalHours, $requiredHours));

                // OPTIONAL — Notify supervisor if exists
                if ($employee->supervisor_id) {
                    $supervisor = User::find($employee->supervisor_id);
                    if ($supervisor) {
                        Mail::to($supervisor->email)
                            ->send(new \App\Mail\SupervisorAlertMail($employee, $totalHours, $requiredHours));
                    }
                }
            }
        }

        $this->info("Weekly reminder process completed.");

        return Command::SUCCESS;
    }


    /**
     * Determine required weekly hours based on employee_type.
     */
    private function getRequiredHours($type)
    {
        return match ($type) {
            'fulltime' => 40,
            'contract' => 20,
            default    => 40, // fallback (you can change this)
        };
    }
}
