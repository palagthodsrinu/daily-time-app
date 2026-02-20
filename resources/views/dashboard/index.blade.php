@extends('layouts.app')

@section('title', 'BBIS Timesheet')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->hasRole('admin');
    $isSupervisor = $user->hasRole('supervisor');
    $isEmployee = $user->hasRole('employee');
@endphp

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Dashboard</h2>
        <p class="text-muted mb-0">
            @if($isAdmin)
                System Overview & Analytics
            @elseif($isSupervisor)
                Team Management & Approvals
            @else
                My Work & Timesheets
            @endif
        </p>
    </div>
    <div class="d-flex">
        @if($isEmployee)
        <a href="{{ route('timesheets.index') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Log Time
        </a>
        @endif
    </div>
</div>

<!-- Role-specific greeting -->
<div class="alert alert-light border mb-4">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <div class="user-avatar-large">
                <span>{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <h5 class="mb-1">Welcome back, {{ $user->first_name }}!</h5>
            <p class="mb-0 text-muted">
                @if($isAdmin)
                    You have full system access to manage users, projects, and monitor overall performance.
                @elseif($isSupervisor)
                    Manage your team's timesheets and track project progress.
                @else
                    Track your time, submit timesheets, and view your project assignments.
                @endif
            </p>
        </div>
    </div>
</div>

<!-- ADMIN DASHBOARD -->
@if($isAdmin)
<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
            <div class="text-muted small mt-2">
                {{ $activeUsers }} active users
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-success">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-number">{{ $totalClients }}</div>
            <div class="stat-label">Clients</div>
            <div class="text-muted small mt-2">
                Managing all client accounts
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-info">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number">{{ $totalProjects }}</div>
            <div class="stat-label">Active Projects</div>
            <div class="text-muted small mt-2">
                Currently running
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pendingTimesheets }}</div>
            <div class="stat-label">Pending Approvals</div>
            <div class="text-muted small mt-2">
                Require attention
            </div>
        </div>
    </div>
</div>

<!-- Additional Admin Metrics -->
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">Monthly Hours</h6>
                <span class="badge bg-primary">{{ now()->format('M Y') }}</span>
            </div>
            <div class="text-center py-4">
                <h2 class="display-4 fw-bold text-primary">{{ $totalHoursThisMonth ?? 0 }}</h2>
                <p class="text-muted mb-0">Total Approved Hours</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">Recent Registrations</h6>
                <a href="{{ route('users.index') }}" class="text-primary small">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentRegistrations ?? [] as $newUser)
                <div class="list-group-item px-0">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar-small me-3">
                            <span>{{ substr($newUser->first_name, 0, 1) }}{{ substr($newUser->last_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $newUser->first_name }} {{ $newUser->last_name }}</div>
                            <small class="text-muted">{{ $newUser->email }}</small>
                        </div>
                        <small class="text-muted">{{ $newUser->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- SUPERVISOR DASHBOARD -->
@if($isSupervisor)
<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $teamMembers }}</div>
            <div class="stat-label">Team Members</div>
            <div class="text-muted small mt-2">
                Under your supervision
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-success">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number">{{ $supervisedProjects }}</div>
            <div class="stat-label">My Projects</div>
            <div class="text-muted small mt-2">
                Active projects
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pendingTimesheets }}</div>
            <div class="stat-label">Pending Approvals</div>
            <div class="text-danger small mt-2">
                @if($urgentApprovals > 0)
                <i class="fas fa-exclamation-circle me-1"></i> {{ $urgentApprovals }} urgent
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">{{ $teamHoursThisWeek ?? 0 }}</div>
            <div class="stat-label">Team Hours This Week</div>
            <div class="text-muted small mt-2">
                Approved hours
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions for Supervisor -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">Quick Actions</h6>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('supervisor.timesheets.index') }}" class="btn btn-outline-primary w-100 h-100 py-3">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <div>Review Timesheets</div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-success w-100 h-100 py-3">
                        <i class="fas fa-project-diagram fa-2x mb-2"></i>
                        <div>View Projects</div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-info w-100 h-100 py-3">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <div>Generate Reports</div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('supervisor.timesheets.export_pdf') }}" class="btn btn-outline-warning w-100 h-100 py-3">
                        <i class="fas fa-file-pdf fa-2x mb-2"></i>
                        <div>Export PDF</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- EMPLOYEE DASHBOARD -->
@if($isEmployee)
<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-primary">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number">{{ $myProjects ?? 0 }}</div>
            <div class="stat-label">My Projects</div>
            <div class="text-muted small mt-2">
                Active assignments
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-success">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $weekHours ?? 0 }}</div>
            <div class="stat-label">Hours This Week</div>
            <div class="text-muted small mt-2">
                Approved time
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-warning">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-number">{{ $myPendingTimesheets ?? 0 }}</div>
            <div class="stat-label">Pending Approval</div>
            <div class="text-muted small mt-2">
                Awaiting review
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon bg-secondary">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stat-number">{{ $draftEntries ?? 0 }}</div>
            <div class="stat-label">Draft Entries</div>
            <div class="text-muted small mt-2">
                Need completion
            </div>
        </div>
    </div>
</div>

<!-- Employee Quick Stats -->
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">Monthly Summary</h6>
                <span class="badge bg-primary">{{ now()->format('M Y') }}</span>
            </div>
            <div class="text-center py-4">
                <h2 class="display-4 fw-bold text-primary">{{ $monthlyHours ?? 0 }}</h2>
                <p class="text-muted mb-0">Total Approved Hours</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">Quick Actions</h6>
            </div>
            <div class="d-grid gap-2 p-3">
                <a href="{{ route('timesheets.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i> Log Time Entry
                </a>
                <a href="{{ route('timesheets.index') }}?status=draft" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-2"></i> Continue Drafts
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Timesheets (Common for all roles) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    @if($isAdmin)
                        Recent Timesheets
                    @elseif($isSupervisor)
                        Team Timesheets
                    @else
                        My Recent Timesheets
                    @endif
                </h5>
                <a href="{{ $isSupervisor ? route('supervisor.timesheets.index') : route('timesheets.index') }}" 
                   class="text-primary text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Date</th>
                            @if($isAdmin || $isSupervisor)
                            <th>Employee</th>
                            @endif
                            <th>Project</th>
                            <th>Time</th>
                            <th>Description</th>
                            <th>Status</th>
                            @if($isAdmin || $isSupervisor)
                            <th class="text-end pe-4">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTimesheets as $sheet)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">
                                    {{ \Carbon\Carbon::parse($sheet->date)->format('M d, Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($sheet->date)->format('D') }}
                                </small>
                            </td>
                            @if($isAdmin || $isSupervisor)
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-small me-2">
                                        <span>
                                            @if($sheet->user)
                                                {{ substr($sheet->user->first_name, 0, 1) }}{{ substr($sheet->user->last_name, 0, 1) }}
                                            @else
                                                ??
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-medium text-dark">{{ $sheet->user->first_name ?? 'Unknown' }} {{ $sheet->user->last_name ?? '' }}</div>
                                        <small class="text-muted">{{ $sheet->user->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            @endif
                            <td>
                                <div class="fw-medium text-dark">{{ $sheet->project->name ?? '-' }}</div>
                                <small class="text-muted">
                                    {{ $sheet->project->client->name ?? 'No client' }}
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ sprintf('%02d', $sheet->hours) }}h {{ sprintf('%02d', $sheet->minutes) }}m
                                </div>
                                <small class="text-muted">
                                    {{ number_format($sheet->hours + ($sheet->minutes / 60), 2) }} hours
                                </small>
                            </td>
                            <td>
                                <div class="text-muted">
                                    @if($sheet->description)
                                        {{ Str::limit($sheet->description, 30) }}
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($sheet->status === 'approved') bg-success
                                    @elseif($sheet->status === 'pending') bg-warning text-dark
                                    @elseif($sheet->status === 'rejected') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst($sheet->status) }}
                                </span>
                            </td>
                            @if($isAdmin || $isSupervisor)
                            <td class="text-end pe-4">
                                @if($sheet->status === 'pending')
                                <div class="d-flex justify-content-end gap-1">
                                    <form method="POST" action="{{ route('supervisor.timesheets.approve', $sheet) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                onclick="return confirm('Approve this timesheet entry?')"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('supervisor.timesheets.reject', $sheet) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Reject this timesheet entry?')"
                                                title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted small">Reviewed</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($isAdmin || $isSupervisor) ? 7 : 6 }}" class="text-center text-muted py-4">
                                <i class="fas fa-clock fa-2x mb-3"></i>
                                <p>No timesheets found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-avatar-large {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
}

.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
}

.dashboard-card .card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 1.5rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}
</style>
@endpush