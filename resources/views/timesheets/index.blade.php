@extends('layouts.app')

@section('title', 'My Timesheets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">My Timesheets</h2>
        <p class="text-muted mb-0">Track and manage your time entries</p>
    </div>
    <div class="d-flex">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimesheetModal">
            <i class="fas fa-plus me-2"></i> New Entry
        </button>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $draftCount ?? $entries->where('status', 'draft')->count() }}</div>
            <div class="stat-label">Draft Entries</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-number">{{ $pendingCount ?? $entries->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending Approval</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $approvedCount ?? $entries->where('status', 'approved')->count() }}</div>
            <div class="stat-label">Approved</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-number">
                {{ $totalHours ?? ($entries->sum('hours') + floor($entries->sum('minutes') / 60)) }}h 
                {{ $totalMinutes ?? ($entries->sum('minutes') % 60) }}m
            </div>
            <div class="stat-label">Total Hours</div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">Time Entries ({{ $entries->total() }})</h5>
        <div class="text-muted small">
            @if($entries->total() > 0)
                Showing {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
            @else
                No entries found
            @endif
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Project</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
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
                            {{ \Carbon\Carbon::parse($entry->date)->format('l') }}
                        </small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="project-avatar-small me-2">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <span class="fw-medium">{{ $entry->project->name ?? 'N/A' }}</span>
                        </div>
                        <small class="text-muted">
                            {{ $entry->project->client->name ?? 'No client' }}
                        </small>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">
                            {{ sprintf('%02d', $entry->hours) }}h {{ sprintf('%02d', $entry->minutes) }}m
                        </div>
                        <small class="text-muted">
                            Total: {{ number_format($entry->hours + ($entry->minutes / 60), 2) }} hours
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
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            @if(in_array($entry->status, ['draft', 'rejected']))
                            <a href="{{ route('timesheets.edit', $entry) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('timesheets.destroy', $entry) }}" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                        onclick="return confirm('Are you sure you want to delete this timesheet entry?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                            @else
                            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" disabled>
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($entries->count() == 0)
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No timesheet entries found</h5>
                            <p class="text-muted mb-0">Get started by adding your first time entry</p>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addTimesheetModal">
                                <i class="fas fa-plus me-2"></i> New Entry
                            </button>
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

<div class="modal fade" id="addTimesheetModal" tabindex="-1" aria-labelledby="addTimesheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="addTimesheetModalLabel">Add Time Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('timesheets.store') }}" id="timesheetEntryForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                id="date" name="date" 
                                value="{{ old('date', date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}" 
                                required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="project_ids" class="form-label">Projects <span class="text-danger">*</span></label>
                            {{-- Integrated Tom Select here --}}
                            <select id="project_ids" name="project_ids[]" multiple placeholder="Select projects..." autocomplete="off" required>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ (is_array(old('project_ids')) && in_array($project->id, old('project_ids'))) ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->client->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="hours" class="form-label">Hours <span class="text-danger">*</span></label>
                            <select class="form-select @error('hours') is-invalid @enderror" 
                                    id="hours" name="hours" required>
                                <option value="">Select hours</option>
                                @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('hours') == $i ? 'selected' : '' }}>
                                    {{ $i }} hours
                                </option>
                                @endfor
                            </select>
                            @error('hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="minutes" class="form-label">Minutes <span class="text-danger">*</span></label>
                            <select class="form-select @error('minutes') is-invalid @enderror" 
                                    id="minutes" name="minutes" required>
                                <option value="">Select minutes</option>
                                <option value="0" {{ old('minutes') == '0' ? 'selected' : '' }}>0 minutes</option>
                                <option value="15" {{ old('minutes') == '15' ? 'selected' : '' }}>15 minutes</option>
                                <option value="30" {{ old('minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                                <option value="45" {{ old('minutes') == '45' ? 'selected' : '' }}>45 minutes</option>
                            </select>
                            @error('minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3"
                                    placeholder="What did you work on? (required)"
                                    maxlength="500"
                                    required>{{ old('description') }}</textarea>
                            <div class="form-text text-end">
                                <span id="charCount">0</span>/500 characters
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <input type="hidden" name="status" id="timesheetStatus" value="draft">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-primary" id="saveDraftBtn">
                        <i class="fas fa-save me-1"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitForApprovalBtn">
                        <i class="fas fa-paper-plane me-1"></i> Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
/* Modern Multiple Select Styling */
.ts-control {
    border-radius: 8px !important;
    padding: 0.6rem 0.75rem !important;
    border: 1px solid #dee2e6 !important;
    box-shadow: none !important;
}
.ts-wrapper.multi .ts-control > div {
    background: #6366f1 !important; /* Matches modern primary colors */
    color: white !important;
    border-radius: 4px !important;
    padding: 2px 8px !important;
    margin: 2px !important;
}
.ts-dropdown {
    border-radius: 8px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

.project-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #8b5cf6, #ec4899);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.pagination .page-item .page-link {
    border-radius: 6px;
    margin: 2px;
    border: 1px solid #dee2e6;
    color: #495057;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Tom Select for Multiple Projects
    const projectSelect = new TomSelect('#project_ids', {
        plugins: ['remove_button', 'clear_button'],
        maxOptions: null,
        placeholder: "Search and select projects...",
        onDropdownOpen: function() {
            this.wrapper.classList.add('dropdown-active');
        }
    });

    // 2. Search Filter for Table
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // 3. Date Restriction
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
    }

    // 4. Character Counter for Description
    const descriptionInput = document.getElementById('description');
    const charCounter = document.getElementById('charCount');
    if (descriptionInput && charCounter) {
        const updateCharCounter = () => {
            const length = descriptionInput.value.length;
            charCounter.textContent = length;
            charCounter.className = length > 500 ? 'text-danger' : (length > 400 ? 'text-warning' : 'text-muted');
        };
        descriptionInput.addEventListener('input', updateCharCounter);
        updateCharCounter();
    }

    // 5. Status Submission Logic
    const form = document.getElementById('timesheetEntryForm');
    const statusInput = document.getElementById('timesheetStatus');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const submitApprovalBtn = document.getElementById('submitForApprovalBtn');

    if (form && statusInput) {
        saveDraftBtn.addEventListener('click', function(e) {
            statusInput.value = 'draft';
        });
        submitApprovalBtn.addEventListener('click', function(e) {
            statusInput.value = 'pending';
        });
    }

    // 6. Project Availability Warning
    if (projectSelect.options.length === 0) {
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning mt-3';
        warningDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> No projects assigned. Please contact your supervisor.`;
        document.getElementById('project_ids').parentNode.appendChild(warningDiv);
        saveDraftBtn.disabled = true;
        submitApprovalBtn.disabled = true;
    }

    @if($errors->any())
        const modal = new bootstrap.Modal(document.getElementById('addTimesheetModal'));
        modal.show();
    @endif
});
</script>
@endpush
@endsection