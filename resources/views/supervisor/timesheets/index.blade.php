@extends('layouts.app')

@section('title', 'Supervisor Timesheets')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Employee Timesheets</h2>
        <p class="text-muted mb-0">Review and approve timesheets for your team</p>
    </div>
    <div class="d-flex">
        <a href="{{ route('supervisor.timesheets.export_pdf', request()->query()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('supervisor.timesheets.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Only</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-2"></i> Apply Filters
                        </button>
                        <a href="{{ route('supervisor.timesheets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Active Filters -->
@if(request()->anyFilled(['from', 'to', 'status']))
<div class="mb-4">
    <div class="d-flex align-items-center">
        <span class="fw-semibold me-2 text-dark">Active Filters:</span>
        <div class="d-flex flex-wrap gap-2">
            @if(request('from'))
            <span class="badge bg-info text-dark">
                From: {{ \Carbon\Carbon::parse(request('from'))->format('M d, Y') }}
                <button type="button" class="btn-close ms-1" onclick="removeFilter('from')"></button>
            </span>
            @endif

            @if(request('to'))
            <span class="badge bg-info text-dark">
                To: {{ \Carbon\Carbon::parse(request('to'))->format('M d, Y') }}
                <button type="button" class="btn-close ms-1" onclick="removeFilter('to')"></button>
            </span>
            @endif

            @if(request('status') && request('status') !== 'all')
            <span class="badge 
                @if(request('status') === 'pending') bg-warning text-dark
                @elseif(request('status') === 'approved') bg-success
                @elseif(request('status') === 'rejected') bg-danger
                @else bg-secondary @endif">
                Status: {{ ucfirst(request('status')) }}
                <button type="button" class="btn-close ms-1" onclick="removeFilter('status')"></button>
            </span>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Success Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Bulk Actions Section (Hidden by default) -->
<div id="bulkActionsSection" class="card mb-3 d-none">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount" class="fw-semibold text-dark">0 timesheets selected</span>
                <small class="text-muted ms-2">(Only pending timesheets can be selected)</small>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('supervisor.timesheets.bulk-approve') }}" id="bulkApproveForm">
                    @csrf
                    <div id="bulkApproveTimesheets"></div>
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve all selected timesheets?')">
                        <i class="fas fa-check me-1"></i> Approve Selected
                    </button>
                </form>
                <form method="POST" action="{{ route('supervisor.timesheets.bulk-reject') }}" id="bulkRejectForm">
                    @csrf
                    <div id="bulkRejectTimesheets"></div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject all selected timesheets?')">
                        <i class="fas fa-times me-1"></i> Reject Selected
                    </button>
                </form>
                <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i> Clear Selection
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Buttons -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('supervisor.timesheets.index', array_merge(request()->query(), ['status' => 'all'])) }}" 
               class="btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                All Timesheets
            </a>
            <a href="{{ route('supervisor.timesheets.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
               class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                <i class="fas fa-clock me-1"></i> Pending Only
            </a>
            <a href="{{ route('supervisor.timesheets.index', array_merge(request()->query(), ['status' => 'approved'])) }}" 
               class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                <i class="fas fa-check me-1"></i> Approved
            </a>
            <a href="{{ route('supervisor.timesheets.index', array_merge(request()->query(), ['status' => 'rejected'])) }}" 
               class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="fas fa-times me-1"></i> Rejected
            </a>
        </div>
    </div>
</div>

<!-- Timesheets Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">
            Timesheet Entries ({{ $entries->total() }})
            @if(request('status') == 'pending')
            <span class="badge bg-warning text-dark ms-2">Pending Approval</span>
            @endif
        </h5>
        <div class="text-muted small">
            @if($entries->total() > 0)
                Showing {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
                @if(request('status') == 'pending')
                • <span class="text-warning">{{ $entries->where('status', 'pending')->count() }} pending</span>
                @endif
            @else
                No entries found
            @endif
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4" width="50">
                        @if(request('status') == 'pending' || !request('status') || request('status') == 'all')
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                        </div>
                        @endif
                    </th>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Project</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr class="{{ $entry->status === 'pending' ? 'table-warning' : '' }}">
                    <td class="ps-4">
                        @if($entry->status === 'pending')
                        <div class="form-check">
                            <input class="form-check-input timesheet-checkbox" type="checkbox" 
                                   value="{{ $entry->id }}" data-status="{{ $entry->status }}">
                        </div>
                        @else
                        <div class="text-center text-muted">
                            <i class="fas fa-minus"></i>
                        </div>
                        @endif
                    </td>
                    <td>
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
                                <span>{{ substr($entry->employee->first_name ?? '', 0, 1) }}{{ substr($entry->employee->last_name ?? '', 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $entry->employee->first_name ?? 'N/A' }} {{ $entry->employee->last_name ?? '' }}</div>
                                <small class="text-muted">{{ $entry->employee->email ?? 'No email' }}</small>
                            </div>
                        </div>
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
                                {{ Str::limit($entry->description, 40) }}
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
                    <td colspan="7" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No timesheet entries found</h5>
                            <p class="text-muted mb-0">
                                @if(request()->anyFilled(['from', 'to', 'status']))
                                    Try adjusting your filters
                                @else
                                    No timesheets from your team members
                                @endif
                            </p>
                            @if(!request()->anyFilled(['from', 'to']))
                            <a href="{{ route('supervisor.timesheets.index') }}?from={{ now()->subDays(30)->format('Y-m-d') }}&to={{ now()->format('Y-m-d') }}&status=all" 
                               class="btn btn-primary mt-3">
                                <i class="fas fa-calendar me-2"></i> View Last 30 Days
                            </a>
                            @endif
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

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.05);
}

#bulkActionsSection {
    border-left: 4px solid var(--primary);
}

/* Quick filter buttons */
.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

/* Pagination Styles */
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

.pagination .page-item {
    margin: 1px;
}

@media (max-width: 768px) {
    .pagination {
        justify-content: center !important;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
        min-width: 36px;
    }
    
    #bulkActionsSection .d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    #bulkActionsSection .btn {
        width: 100%;
    }
    
    .d-flex.flex-wrap.gap-2 {
        justify-content: center;
    }
}

.badge {
    font-size: 0.75rem;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}
</style>
@endpush

@push('scripts')
<script>
let selectedTimesheets = new Set();

function updateBulkActions() {
    const selectedCount = selectedTimesheets.size;
    const bulkActionsSection = document.getElementById('bulkActionsSection');
    const selectedCountElement = document.getElementById('selectedCount');
    const bulkApproveContainer = document.getElementById('bulkApproveTimesheets');
    const bulkRejectContainer = document.getElementById('bulkRejectTimesheets');
    
    // Update selected count
    selectedCountElement.textContent = `${selectedCount} timesheet${selectedCount !== 1 ? 's' : ''} selected`;
    
    // Update hidden inputs for forms
    bulkApproveContainer.innerHTML = '';
    bulkRejectContainer.innerHTML = '';
    
    selectedTimesheets.forEach(timesheetId => {
        bulkApproveContainer.innerHTML += `<input type="hidden" name="timesheet_ids[]" value="${timesheetId}">`;
        bulkRejectContainer.innerHTML += `<input type="hidden" name="timesheet_ids[]" value="${timesheetId}">`;
    });
    
    // Show/hide bulk actions section
    if (selectedCount > 0) {
        bulkActionsSection.classList.remove('d-none');
    } else {
        bulkActionsSection.classList.add('d-none');
    }
    
    // Update select all checkbox state
    const totalPending = document.querySelectorAll('.timesheet-checkbox').length;
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        if (selectedCount === totalPending && totalPending > 0) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedCount > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
}

function clearSelection() {
    selectedTimesheets.clear();
    document.querySelectorAll('.timesheet-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

// Select All functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.timesheet-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
                if (isChecked) {
                    selectedTimesheets.add(checkbox.value);
                } else {
                    selectedTimesheets.delete(checkbox.value);
                }
            });
            updateBulkActions();
        });
    }
});

// Individual checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('timesheet-checkbox')) {
            if (e.target.checked) {
                selectedTimesheets.add(e.target.value);
            } else {
                selectedTimesheets.delete(e.target.value);
            }
            updateBulkActions();
        }
    });
});

function removeFilter(filterName) {
    const url = new URL(window.location.href);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

// Set default date range if not set
document.addEventListener('DOMContentLoaded', function() {
    const fromDate = document.getElementById('from');
    const toDate = document.getElementById('to');
    
    if (!fromDate.value && !toDate.value) {
        // Default to last 30 days
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        fromDate.value = thirtyDaysAgo.toISOString().split('T')[0];
        toDate.value = new Date().toISOString().split('T')[0];
    }
});

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
@endsection