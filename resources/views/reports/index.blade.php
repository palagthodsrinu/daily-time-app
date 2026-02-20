@extends('layouts.app')

@section('title', 'Timesheet Reports')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Timesheet Reports</h2>
        <!-- <p class="text-muted mb-0">Analyze and filter timesheet data</p> -->
    </div>
    <div class="d-flex">
        <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
            <i class="fas fa-filter me-2"></i> Filters
        </button>
        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filters Section -->
<div class="collapse show mb-4" id="filtersCollapse">
    <div class="table-container">
        <div class="p-4">
            <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
                <div class="row g-3" style="overflow: visible !important;">
                    <!-- Search -->
                    <div class="col-12">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" 
                               id="search" name="search" value="{{ request('search') }}"
                               placeholder="Search by name, email, role or status...">
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-4">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" 
                               id="from_date" name="from_date" value="{{ request('from_date') }}"
                               max="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" 
                               id="to_date" name="to_date" value="{{ request('to_date') }}"
                               max="{{ now()->format('Y-m-d') }}">
                    </div>

                    <!-- Employee Type Filter -->
                    <div class="col-md-4">
                        <label for="employee_type" class="form-label">Employee Type</label>
                        <select class="form-select" id="employee_type" name="employee_type">
                            <option value="">All Types</option>
                            <option value="fulltime" {{ request('employee_type') == 'fulltime' ? 'selected' : '' }}>Full Time</option>
                            <option value="contract" {{ request('employee_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>

                    <!-- Supervisors Selection -->
                    <div class="col-md-6">
                        <label for="supervisorSearch" class="form-label">Select Supervisors</label>
                        
                        <div class="search-container position-relative" style="z-index: 200;"> 
                            <input type="text" class="form-control" 
                                id="supervisorSearch" 
                                placeholder="Click to view all supervisors or type to search..."
                                autocomplete="off">
                            <div class="dropdown-results" id="supervisorResults"></div>
                        </div>

                        <!-- Selected Supervisors Display -->
                        <div id="supervisorSelectedList" class="selected-items mt-2">
                            @foreach($supervisors->whereIn('id', $selectedSupervisors) as $supervisor)
                                <span class="badge bg-warning text-dark mr-1 mb-1">
                                    <i class="fas fa-user-tie me-1"></i>
                                    {{ $supervisor->first_name }} {{ $supervisor->last_name }}
                                    <button type="button" class="btn-close ms-1" 
                                            onclick="removeSelectedSupervisor({{ $supervisor->id }})"></button>
                                    <input type="hidden" name="supervisors[]" value="{{ $supervisor->id }}">
                                </span>
                            @endforeach
                        </div>

                        <div class="form-text">Click to view and select supervisors</div>
                    </div>

                    <!-- Employees Selection -->
                    <div class="col-md-6">
                        <label for="employeeSearch" class="form-label">Select Employees</label>
                        
                        <div class="search-container position-relative" style="z-index: 200;">
                            <input type="text" class="form-control" 
                                id="employeeSearch" 
                                placeholder="Click to view all employees or type to search..."
                                autocomplete="off">
                            <div class="dropdown-results" id="employeeResults"></div>
                        </div>

                        <!-- Selected Employees Display -->
                        <div id="employeeSelectedList" class="selected-items mt-2">
                            @foreach($employees->whereIn('id', $selectedEmployees) as $employee)
                                <span class="badge bg-primary mr-1 mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    <button type="button" class="btn-close btn-close-white ms-1" 
                                            onclick="removeSelectedEmployee({{ $employee->id }})"></button>
                                    <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                                </span>
                            @endforeach
                        </div>

                        <div class="form-text" id="employeeSearchHelp">Click to view and select employees</div>
                    </div>

                    <!-- Form Actions -->
                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i> Apply Filters
                                </button>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-refresh me-2"></i> Reset
                                </a>
                            </div>
                            <div class="text-muted small">
                                Found {{ $entries->total() }} entries
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Active Filters -->
@if(request()->anyFilled(['search', 'from_date', 'to_date', 'employee_type', 'employees', 'supervisors']))
<div class="mb-4">
    <div class="d-flex align-items-center">
        <span class="fw-semibold me-2 text-dark">Active Filters:</span>
        <div class="d-flex flex-wrap gap-2">
            @if(request('search'))
            <span class="badge bg-primary">
                Search: "{{ request('search') }}"
                <button type="button" class="btn-close btn-close-white ms-1" onclick="removeFilter('search')"></button>
            </span>
            @endif

            @if(request('from_date'))
            <span class="badge bg-info text-dark">
                From: {{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }}
                <button type="button" class="btn-close ms-1" onclick="removeFilter('from_date')"></button>
            </span>
            @endif

            @if(request('to_date'))
            <span class="badge bg-info text-dark">
                To: {{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}
                <button type="button" class="btn-close ms-1" onclick="removeFilter('to_date')"></button>
            </span>
            @endif

            @if(request('employee_type'))
            <span class="badge bg-secondary">
                Type: {{ request('employee_type') == 'fulltime' ? 'Full Time' : 'Contract' }}
                <button type="button" class="btn-close btn-close-white ms-1" onclick="removeFilter('employee_type')"></button>
            </span>
            @endif

            @foreach($allSelectedUsers as $userId)
                @php 
                    $user = $employees->firstWhere('id', $userId) ?? $supervisors->firstWhere('id', $userId);
                @endphp
                @if($user)
                <span class="badge {{ in_array($userId, $selectedSupervisors) ? 'bg-warning text-dark' : 'bg-primary' }}">
                    {{ in_array($userId, $selectedSupervisors) ? 'Supervisor' : 'Employee' }}: {{ $user->first_name }} {{ $user->last_name }}
                    <button type="button" class="btn-close {{ in_array($userId, $selectedSupervisors) ? '' : 'btn-close-white' }} ms-1" onclick="removeUserFilter({{ $userId }})"></button>
                </span>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Total Hours Summary -->
@php
    // Calculate total hours from the entries
    $totalHours = 0;
    $totalMinutes = 0;
    $totalDecimalHours = 0;
    
    foreach ($entries as $entry) {
        $totalHours += $entry->hours;
        $totalMinutes += $entry->minutes;
        $totalDecimalHours += ($entry->hours + ($entry->minutes / 60));
    }
    
    // Convert excess minutes to hours
    $totalHours += floor($totalMinutes / 60);
    $totalMinutes = $totalMinutes % 60;
@endphp

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Hours</h6>
                        <h3 class="card-title fw-bold text-primary">{{ number_format($totalDecimalHours, 2) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-clock fa-2x text-primary"></i>
                    </div>
                </div>
                <p class="card-text small text-muted mb-0">{{ $totalHours }}h {{ $totalMinutes }}m across {{ $entries->total() }} entries</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Entries</h6>
                        <h3 class="card-title fw-bold text-success">{{ $entries->total() }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-list fa-2x text-success"></i>
                    </div>
                </div>
                <p class="card-text small text-muted mb-0">Showing {{ $entries->count() }} entries on this page</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Avg Hours/Entry</h6>
                        <h3 class="card-title fw-bold text-warning">
                            @if($entries->total() > 0)
                                {{ number_format($totalDecimalHours / $entries->total(), 2) }}
                            @else
                                0.00
                            @endif
                        </h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-chart-bar fa-2x text-warning"></i>
                    </div>
                </div>
                <p class="card-text small text-muted mb-0">Average hours per timesheet entry</p>
            </div>
        </div>
    </div>
</div>

<!-- Timesheets Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">Timesheet Entries</h5>
        <div class="text-muted small">
            Showing {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
            @if($entries->total() > 0)
                <span class="ms-2 fw-semibold text-primary">
                    (Total: {{ number_format($totalDecimalHours, 2) }} hours)
                </span>
            @endif
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Date</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Employee Type</th>
                    <th>Project</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold text-dark">
                            {{ \Carbon\Carbon::parse($entry->date)->format('M d, Y') }}
                        </div>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($entry->date)->format('D') }}
                        </small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-small me-2">
                                <span>{{ substr($entry->user->first_name, 0, 1) }}{{ substr($entry->user->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $entry->user->first_name }} {{ $entry->user->last_name }}</div>
                                <small class="text-muted">{{ $entry->user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $isEmployee = $employees->contains('id', $entry->user_id);
                            $isSupervisor = $supervisors->contains('id', $entry->user_id);
                        @endphp
                        <span class="badge {{ $isEmployee ? 'bg-primary' : ($isSupervisor ? 'bg-info' : 'bg-secondary') }}">
                            {{ $isEmployee ? 'Employee' : ($isSupervisor ? 'Supervisor' : 'Unknown') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $entry->user->employee_type == 'fulltime' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $entry->user->employee_type == 'fulltime' ? 'Full Time' : 'Contract' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $entry->project->name ?? 'N/A' }}</div>
                        <small class="text-muted">
                            {{ $entry->project->client->name ?? 'No client' }}
                        </small>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">
                            {{ sprintf('%02d', $entry->hours) }}h {{ sprintf('%02d', $entry->minutes) }}m
                        </div>
                        <small class="text-muted">
                            {{ number_format($entry->hours + ($entry->minutes / 60), 2) }} hours
                        </small>
                    </td>
                    <td>
                        <div class="text-muted">
                            @if($entry->description)
                                {{ Str::limit($entry->description, 50) }}
                            @else
                                <span class="text-muted">No description</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge 
                            @if($entry->status === 'approved') bg-success
                            @elseif($entry->status === 'pending') bg-warning text-dark
                            @elseif($entry->status === 'rejected') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst($entry->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
                
                @if($entries->count() == 0)
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No timesheet entries found</h5>
                            <p class="text-muted mb-0">Try adjusting your filters or search criteria</p>
                            <a href="{{ route('reports.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-refresh me-2"></i> Clear Filters
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if($entries->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top gap-3">
    <div class="text-muted small">
        Showing {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
        @if($entries->total() > 0)
            <span class="ms-2 fw-semibold text-primary">
                (Total: {{ number_format($totalDecimalHours, 2) }} hours)
            </span>
        @endif
    </div>
    <div class="d-flex justify-content-center justify-content-md-end w-100 w-md-auto">
        <nav aria-label="Timesheet entries pagination">
            {{ $entries->onEachSide(1)->links('pagination::bootstrap-5') }}
        </nav>
    </div>
</div>
@endif
</div>

@push('styles')
<style>
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

.badge .btn-close {
    font-size: 0.7rem;
    padding: 0.5rem;
}

.badge .btn-close:hover {
    opacity: 1;
}

.selected-items .badge {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
    display: inline-flex;
    align-items: center;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.search-container {
    position: relative;
}

.dropdown-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1050;
    display: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    margin-top: 0.25rem;
}

.dropdown-results .dropdown-item {
    border: none;
    border-radius: 0;
    text-align: left;
    cursor: pointer;
    padding: 0.75rem 1rem;
    width: 100%;
    background: none;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.dropdown-results .dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-results .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #1e2125;
}

.dropdown-results .dropdown-item .user-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.125rem;
}

.dropdown-results .dropdown-item .user-email {
    font-size: 0.875rem;
    color: #6c757d;
}

.dropdown-results .dropdown-item:active {
    background-color: #0d6efd;
    color: white;
}

.dropdown-results .dropdown-item:active .user-name,
.dropdown-results .dropdown-item:active .user-email {
    color: white;
}

.loading-spinner {
    text-align: center;
    padding: 1rem;
    color: #6c757d;
    font-style: italic;
    border-bottom: 1px solid #f8f9fa;
}

.no-results {
    text-align: center;
    padding: 1rem;
    color: #6c757d;
    font-style: italic;
}

/* Ensure the dropdown appears above other elements */
.search-container {
    z-index: 1051;
}

/* Style for the search inputs when dropdown is open */
.search-container.active .form-control {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
#filtersCollapse, 
.table-container {
    overflow: visible !important;
}
.pagination {
    margin-bottom: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination .page-item .page-link {
    border-radius: 6px;
    margin: 2px;
    border: 1px solid #dee2e6;
    color: #495057;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    min-width: 42px;
    text-align: center;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination .page-item:not(.active) .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #495057;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Ensure proper alignment */
.pagination .page-item {
    margin: 1px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pagination {
        justify-content: center !important;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
        min-width: 36px;
    }
    
    /* Summary cards responsive */
    .card-body h3 {
        font-size: 1.5rem;
    }
}

/* Summary cards styling */
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded - initializing search functionality');

    // Set default date range if not set
    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    
    if (!fromDate.value && !toDate.value) {
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        fromDate.value = thirtyDaysAgo.toISOString().split('T')[0];
        toDate.value = new Date().toISOString().split('T')[0];
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ================= SUPERVISOR SEARCH =================
    const supervisorSearch = document.getElementById('supervisorSearch');
    const supervisorResults = document.getElementById('supervisorResults');
    const supervisorContainer = supervisorSearch?.closest('.search-container');

    if (supervisorSearch && supervisorResults) {
        console.log('✅ Initializing supervisor search');

        // Load all supervisors on focus
        supervisorSearch.addEventListener('focus', function() {
            supervisorContainer?.classList.add('active');
            loadAllSupervisors();
        });

        // Search supervisors on input
        const supervisorSearchHandler = debounce(function(e) {
            const query = e.target.value.trim();
            console.log('🔍 Supervisor search query:', query);

            if (query.length === 0) {
                loadAllSupervisors();
                return;
            }

            // Show loading
            supervisorResults.innerHTML = '<div class="loading-spinner">Searching supervisors...</div>';
            supervisorResults.style.display = 'block';

            const url = `{{ route('reports.search.supervisors') }}?q=${encodeURIComponent(query)}`;
            console.log('📡 Fetching from:', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('📨 Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Received supervisor data:', data);
                displaySupervisorResults(data);
            })
            .catch(error => {
                console.error('❌ Supervisor search error:', error);
                supervisorResults.innerHTML = '<div class="dropdown-item text-danger">Error searching supervisors</div>';
                supervisorResults.style.display = 'block';
            });
        }, 300);

        supervisorSearch.addEventListener('input', supervisorSearchHandler);

        // Handle blur to close dropdown
        supervisorSearch.addEventListener('blur', function() {
            setTimeout(() => {
                supervisorResults.style.display = 'none';
                supervisorContainer?.classList.remove('active');
            }, 200);
        });
    }

    // Function to load all supervisors
    function loadAllSupervisors() {
        console.log('📋 Loading all supervisors');
        
        supervisorResults.innerHTML = '<div class="loading-spinner">Loading supervisors...</div>';
        supervisorResults.style.display = 'block';

        const url = `{{ route('reports.search.supervisors') }}?q=`;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            displaySupervisorResults(data);
        })
        .catch(error => {
            console.error('❌ Error loading supervisors:', error);
            supervisorResults.innerHTML = '<div class="dropdown-item text-danger">Error loading supervisors</div>';
            supervisorResults.style.display = 'block';
        });
    }

    // ================= EMPLOYEE SEARCH =================
    const employeeSearch = document.getElementById('employeeSearch');
    const employeeResults = document.getElementById('employeeResults');
    const employeeContainer = employeeSearch?.closest('.search-container');

    if (employeeSearch && employeeResults) {
        console.log('✅ Initializing employee search');

        // Load employees on focus
        employeeSearch.addEventListener('focus', function() {
            employeeContainer?.classList.add('active');
            loadEmployees('');
        });

        // Search employees on input
        const employeeSearchHandler = debounce(function(e) {
            const query = e.target.value.trim();
            console.log('🔍 Employee search query:', query);
            loadEmployees(query);
        }, 300);

        employeeSearch.addEventListener('input', employeeSearchHandler);

        // Handle blur to close dropdown
        employeeSearch.addEventListener('blur', function() {
            setTimeout(() => {
                employeeResults.style.display = 'none';
                employeeContainer?.classList.remove('active');
            }, 200);
        });
    }

    // Function to load employees (with or without supervisors filter)
    function loadEmployees(query = '') {
        console.log('📋 Loading employees, query:', query);
        
        employeeResults.innerHTML = '<div class="loading-spinner">Loading employees...</div>';
        employeeResults.style.display = 'block';

        // Get selected supervisors (if any)
        const supervisorInputs = document.querySelectorAll('input[name="supervisors[]"]');
        const selectedSupervisors = Array.from(supervisorInputs).map(input => input.value).filter(Boolean);
        
        console.log('👥 Selected supervisors:', selectedSupervisors);

        // Build URL - include supervisors only if any are selected
        let url = `{{ route('reports.search.employees') }}?q=${encodeURIComponent(query)}`;
        if (selectedSupervisors.length > 0) {
            url += `&supervisors=${selectedSupervisors.join(',')}`;
        }

        console.log('📡 Fetching from:', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('📨 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Received employee data:', data);
            displayEmployeeResults(data);
        })
        .catch(error => {
            console.error('❌ Employee search error:', error);
            employeeResults.innerHTML = '<div class="dropdown-item text-danger">Error searching employees</div>';
            employeeResults.style.display = 'block';
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            if (supervisorResults) {
                supervisorResults.style.display = 'none';
                supervisorContainer?.classList.remove('active');
            }
            if (employeeResults) {
                employeeResults.style.display = 'none';
                employeeContainer?.classList.remove('active');
            }
        }
    });

    console.log('✅ Search functionality fully initialized');
});

// ================= DISPLAY FUNCTIONS =================

function displaySupervisorResults(data) {
    const supervisorResults = document.getElementById('supervisorResults');
    supervisorResults.innerHTML = '';

    if (data.length === 0) {
        supervisorResults.innerHTML = '<div class="no-results">No supervisors found</div>';
    } else {
        data.forEach(user => {
            // Check if already selected
            const isSelected = document.querySelector(`input[name="supervisors[]"][value="${user.id}"]`);
            if (!isSelected) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'dropdown-item';
                button.innerHTML = `
                    <div class="user-name">${user.name}</div>
                    <div class="user-email">${user.email}</div>
                `;
                button.onclick = function() {
                    selectSupervisor(user);
                };
                supervisorResults.appendChild(button);
            }
        });

        if (supervisorResults.children.length === 0) {
            supervisorResults.innerHTML = '<div class="no-results">All supervisors are already selected</div>';
        }
    }
    supervisorResults.style.display = 'block';
}

function displayEmployeeResults(data) {
    const employeeResults = document.getElementById('employeeResults');
    employeeResults.innerHTML = '';

    if (data.length === 0) {
        employeeResults.innerHTML = '<div class="no-results">No employees found</div>';
    } else {
        data.forEach(user => {
            // Check if already selected
            const isSelected = document.querySelector(`input[name="employees[]"][value="${user.id}"]`);
            if (!isSelected) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'dropdown-item';
                button.innerHTML = `
                    <div class="user-name">${user.name}</div>
                    <div class="user-email">${user.email}</div>
                `;
                button.onclick = function() {
                    selectEmployee(user);
                };
                employeeResults.appendChild(button);
            }
        });

        if (employeeResults.children.length === 0) {
            employeeResults.innerHTML = '<div class="no-results">All employees are already selected</div>';
        }
    }
    employeeResults.style.display = 'block';
}

// ================= SELECTION FUNCTIONS =================

function selectSupervisor(user) {
    console.log('✅ Selecting supervisor:', user);
    
    // Check max limit
    const totalSelected = document.querySelectorAll('input[name="supervisors[]"], input[name="employees[]"]').length;
    if (totalSelected >= 5) {
        alert('Maximum 5 users allowed (employees + supervisors combined).');
        return;
    }

    const container = document.getElementById('supervisorSelectedList');
    const badge = document.createElement('span');
    badge.className = 'badge bg-warning text-dark mr-1 mb-1';
    badge.innerHTML = `
        <i class="fas fa-user-tie me-1"></i>
        ${user.name}
        <button type="button" class="btn-close ms-1" 
                onclick="removeSelectedSupervisor(${user.id})"></button>
        <input type="hidden" name="supervisors[]" value="${user.id}">
    `;
    container.appendChild(badge);

    // Clear search and close dropdown
    document.getElementById('supervisorResults').style.display = 'none';
    document.getElementById('supervisorSearch').value = '';
    document.querySelector('.search-container')?.classList.remove('active');

    updateSelectionCount();
    
    // Refresh employee list if employees dropdown is open
    const employeeResults = document.getElementById('employeeResults');
    if (employeeResults && employeeResults.style.display === 'block') {
        const employeeSearch = document.getElementById('employeeSearch');
        loadEmployees(employeeSearch.value);
    }
}

function selectEmployee(user) {
    console.log('✅ Selecting employee:', user);
    
    // Check max limit
    const totalSelected = document.querySelectorAll('input[name="supervisors[]"], input[name="employees[]"]').length;
    if (totalSelected >= 5) {
        alert('Maximum 5 users allowed (employees + supervisors combined).');
        return;
    }

    const container = document.getElementById('employeeSelectedList');
    const badge = document.createElement('span');
    badge.className = 'badge bg-primary mr-1 mb-1';
    badge.innerHTML = `
        <i class="fas fa-user me-1"></i>
        ${user.name}
        <button type="button" class="btn-close btn-close-white ms-1" 
                onclick="removeSelectedEmployee(${user.id})"></button>
        <input type="hidden" name="employees[]" value="${user.id}">
    `;
    container.appendChild(badge);

    // Clear search and close dropdown
    document.getElementById('employeeResults').style.display = 'none';
    document.getElementById('employeeSearch').value = '';
    document.querySelector('.search-container')?.classList.remove('active');

    updateSelectionCount();
}

function removeSelectedSupervisor(id) {
    console.log('🗑️ Removing supervisor:', id);
    const container = document.getElementById('supervisorSelectedList');
    const input = container.querySelector(`input[value="${id}"]`);
    if (input && input.parentElement) {
        input.parentElement.remove();
        updateSelectionCount();
        
        // Refresh employee list if employees dropdown is open
        const employeeResults = document.getElementById('employeeResults');
        if (employeeResults && employeeResults.style.display === 'block') {
            const employeeSearch = document.getElementById('employeeSearch');
            loadEmployees(employeeSearch.value);
        }
    }
}

function removeSelectedEmployee(id) {
    console.log('🗑️ Removing employee:', id);
    const container = document.getElementById('employeeSelectedList');
    const input = container.querySelector(`input[value="${id}"]`);
    if (input && input.parentElement) {
        input.parentElement.remove();
        updateSelectionCount();
    }
}

// ================= EXISTING FUNCTIONS =================

function getSelectedUserIds() {
    const employeeInputs = document.querySelectorAll('input[name="employees[]"]');
    const supervisorInputs = document.querySelectorAll('input[name="supervisors[]"]');
    
    const employees = Array.from(employeeInputs).map(input => input.value);
    const supervisors = Array.from(supervisorInputs).map(input => input.value);
    
    return [...employees, ...supervisors];
}

function updateSelectionCount() {
    const selectedIds = getSelectedUserIds();
    const count = selectedIds.length;
    const counter = document.getElementById('selectedCount');
    const counterContainer = document.getElementById('selectionCounter');
    
    if (counter && counterContainer) {
        counter.textContent = count;
        
        if (count > 5) {
            counterContainer.className = 'alert alert-danger py-2 mb-0';
            // Remove excess items
            const allItems = [...document.querySelectorAll('input[name="employees[]"]'), ...document.querySelectorAll('input[name="supervisors[]"]')];
            for (let i = 5; i < allItems.length; i++) {
                allItems[i].parentElement.remove();
            }
            updateSelectionCount();
            alert('Maximum 5 users allowed. Extra selections have been removed.');
        } else {
            counterContainer.className = count > 0 ? 'alert alert-success py-2 mb-0' : 'alert alert-info py-2 mb-0';
        }
    }
}

function removeFilter(filterName) {
    const url = new URL(window.location.href);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

function removeUserFilter(userId) {
    const url = new URL(window.location.href);
    
    const employees = url.searchParams.getAll('employees[]');
    const updatedEmployees = employees.filter(id => id != userId);
    
    const supervisors = url.searchParams.getAll('supervisors[]');
    const updatedSupervisors = supervisors.filter(id => id != userId);
    
    url.searchParams.delete('employees[]');
    url.searchParams.delete('supervisors[]');
    
    updatedEmployees.forEach(id => url.searchParams.append('employees[]', id));
    updatedSupervisors.forEach(id => url.searchParams.append('supervisors[]', id));
    
    window.location.href = url.toString();
}

function setDateRange(days) {
    const toDate = new Date();
    const fromDate = new Date();
    fromDate.setDate(fromDate.getDate() - days);
    
    document.getElementById('from_date').value = fromDate.toISOString().split('T')[0];
    document.getElementById('to_date').value = toDate.toISOString().split('T')[0];
    
    document.getElementById('reportForm').submit();
}

// Form submission validation
document.getElementById('reportForm')?.addEventListener('submit', function(e) {
    const selectedCount = getSelectedUserIds().length;
    if (selectedCount > 5) {
        e.preventDefault();
        alert('Please select maximum 5 users (employees + supervisors combined).');
        return false;
    }
});
</script>
@endpush
@endsection