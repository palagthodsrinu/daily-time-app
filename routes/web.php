<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectAssignmentController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\SupervisorTimesheetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\MicrosoftController;
/*
|--------------------------------------------------------------------------
| Public Routes (No Auth)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));


// web.php (Corrected)

// Show email-only login form
Route::get('/login', [LoginController::class, 'showLogin'])->name('login'); //

// POST receives email from login form and directs to LoginController's submitEmail (which handles the MS redirect)
Route::post('/login', [LoginController::class, 'submitEmail'])->name('login.email'); // <--- ADD/UPDATE THIS LINE

// Logout (POST)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); //

/*
|--------------------------------------------------------------------------
| Microsoft OAuth Routes (Must NOT be inside auth middleware)
|--------------------------------------------------------------------------
*/
Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])
    ->name('microsoft.redirect'); //

Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])
    ->name('microsoft.callback'); //
// Logout (POST)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | TIMESHEET ROUTES (Employee & Supervisor Access)
    |--------------------------------------------------------------------------
    */
    Route::get('/timesheets', [TimesheetController::class,'index'])->name('timesheets.index');
    Route::post('/timesheets', [TimesheetController::class,'store'])->name('timesheets.store');
    Route::get('/timesheets/{id}/edit', [TimesheetController::class,'edit'])->name('timesheets.edit');
    Route::put('/timesheets/{timesheet}', [TimesheetController::class,'update'])->name('timesheets.update');
    Route::delete('/timesheets/{timesheet}', [TimesheetController::class,'destroy'])->name('timesheets.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {

        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('projects', ProjectController::class)->except(['show']);
        Route::resource('assignments', ProjectAssignmentController::class)->except(['show']);

        // Full Reports module (Admin only)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        // AJAX Search APIs
        Route::get('/reports/search/supervisors', [ReportController::class, 'searchSupervisors'])
            ->name('reports.search.supervisors');

        Route::get('/reports/search/employees', [ReportController::class, 'searchEmployees'])
            ->name('reports.search.employees');
    });


    /*
    |--------------------------------------------------------------------------
    | SUPERVISOR ONLY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:supervisor'])->group(function () {

        Route::get('/supervisor/timesheets', [SupervisorTimesheetController::class,'index'])
            ->name('supervisor.timesheets.index');

        Route::post('/supervisor/timesheets/{timesheet}/approve', 
            [SupervisorTimesheetController::class,'approve'])
            ->name('supervisor.timesheets.approve');

        Route::post('/supervisor/timesheets/{timesheet}/reject', 
            [SupervisorTimesheetController::class,'reject'])
            ->name('supervisor.timesheets.reject');

        Route::get('/supervisor/timesheets/export/pdf', 
            [SupervisorTimesheetController::class,'exportPdf'])
            ->name('supervisor.timesheets.export_pdf');

        // Bulk approval operations
        Route::post('/supervisor/timesheets/bulk-approve', 
            [SupervisorTimesheetController::class,'bulkApprove'])
            ->name('supervisor.timesheets.bulk-approve');

        Route::post('/supervisor/timesheets/bulk-reject', 
            [SupervisorTimesheetController::class,'bulkReject'])
            ->name('supervisor.timesheets.bulk-reject');
    });

});
