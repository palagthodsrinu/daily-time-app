@extends('layouts.app')

@section('title', 'Create Assignment')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Create New Assignment</h2>
        <p class="text-muted mb-0">Assign employees to projects with supervisors</p>
    </div>
    <div>
        <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Assignments
        </a>
    </div>
</div>

<!-- Create Assignment Form -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('assignments.store') }}" id="assignmentForm">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Assignment Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-link me-2 text-primary"></i>Assignment Details
                            </h5>
                        </div>
                       
                        
                        <div class="col-6">
                            <label for="employee_ids" class="form-label">Employees <span class="text-danger">*</span></label>
                            {{-- Tom Select for multiple employee selection --}}
                            <select id="employee_ids" name="employee_ids[]" multiple placeholder="Search and select employees..." autocomplete="off" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ (is_array(old('employee_ids')) && in_array($employee->id, old('employee_ids'))) ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if($employees->count() == 0)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No employees available. Please <a href="{{ route('users.create') }}">create employee users</a> first.
                            </div>
                            @else
                            <div class="form-text">Select one or more employees to assign to this project</div>
                            @endif
                        </div>
                         <div class="col-md-6">
                            <label for="supervisor_id" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <select class="form-select @error('supervisor_id') is-invalid @enderror" 
                                    id="supervisor_id" name="supervisor_id" required>
                                <option value="">Select a supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->first_name }} {{ $supervisor->last_name }} ({{ $supervisor->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($supervisors->count() == 0)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No supervisors available. Please <a href="{{ route('users.create') }}">create supervisor users</a> first.
                            </div>
                            @else
                            <div class="form-text">Select the supervisor for this assignment</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id" required>
                                <option value="">Select a project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} - {{ $project->client->name ?? 'No client' }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($projects->count() == 0)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No projects available. Please <a href="{{ route('projects.create') }}">create a project</a> first.
                            </div>
                            @else
                            <div class="form-text">Select the project for this assignment</div>
                            @endif
                        </div>
                        
                        <!-- Assignment Status -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-toggle-on me-2 text-primary"></i>Assignment Status
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Assignment
                                </label>
                            </div>
                            <div class="form-text">
                                Active assignments allow employees to log time against this project
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn"
                                        {{ ($projects->count() == 0 || $employees->count() == 0 || $supervisors->count() == 0) ? 'disabled' : '' }}>
                                    <i class="fas fa-link me-2"></i> Create Assignment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
/* Modern Tom Select Styling */
.ts-control {
    border-radius: 8px !important;
    padding: 0.6rem 0.75rem !important;
    border: 1px solid #dee2e6 !important;
    box-shadow: none !important;
    min-height: 46px !important;
}

.ts-wrapper.multi .ts-control > div {
    background: #6366f1 !important;
    color: white !important;
    border-radius: 4px !important;
    padding: 2px 8px !important;
    margin: 2px !important;
}

.ts-wrapper.multi .ts-control > div.active {
    background: #4f46e5 !important;
}

.ts-dropdown {
    border-radius: 8px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #dee2e6 !important;
    margin-top: 4px !important;
}

.ts-dropdown .option {
    padding: 8px 12px !important;
}

.ts-dropdown .option:hover {
    background-color: #f8f9fa !important;
}

.ts-dropdown .option.selected {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

.ts-dropdown .create:hover,
.ts-dropdown .option:hover {
    background-color: #f8f9fa !important;
}

/* Error state */
.is-invalid ~ .ts-control {
    border-color: #dc3545 !important;
}

/* Disabled state */
.ts-control.disabled {
    background-color: #e9ecef !important;
    opacity: 0.6 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for multiple employee selection
    const employeeSelect = new TomSelect('#employee_ids', {
        plugins: ['remove_button', 'clear_button'],
        maxOptions: null,
        maxItems: null,
        placeholder: "Search and select employees...",
        create: false,
        allowEmptyOption: false,
        closeAfterSelect: true,
        onDropdownOpen: function() {
            this.wrapper.classList.add('dropdown-active');
        },
        onDropdownClose: function() {
            this.wrapper.classList.remove('dropdown-active');
        },
        render: {
            option: function(data, escape) {
                return '<div class="d-flex align-items-center">' +
                    '<div class="me-2" style="width: 32px; height: 32px; border-radius: 50%; background: #6366f1; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">' +
                    escape(data.text.charAt(0)) +
                    '</div>' +
                    '<div>' +
                    '<div class="fw-medium">' + escape(data.text.split('(')[0].trim()) + '</div>' +
                    '<small class="text-muted">' + (data.text.match(/\((.*?)\)/) ? escape(data.text.match(/\((.*?)\)/)[1]) : '') + '</small>' +
                    '</div>' +
                    '</div>';
            },
            item: function(data, escape) {
                return '<div>' + escape(data.text.split('(')[0].trim()) + '</div>';
            }
        }
    });

    // Initialize Tom Select for project (single select)
    const projectSelect = new TomSelect('#project_id', {
        create: false,
        placeholder: "Select a project...",
        allowEmptyOption: false,
        onDropdownOpen: function() {
            this.wrapper.classList.add('dropdown-active');
        },
        onDropdownClose: function() {
            this.wrapper.classList.remove('dropdown-active');
        }
    });

    // Initialize Tom Select for supervisor (single select)
    const supervisorSelect = new TomSelect('#supervisor_id', {
        create: false,
        placeholder: "Select a supervisor...",
        allowEmptyOption: false,
        onDropdownOpen: function() {
            this.wrapper.classList.add('dropdown-active');
        },
        onDropdownClose: function() {
            this.wrapper.classList.remove('dropdown-active');
        }
    });

    // Form validation and submission
    const form = document.getElementById('assignmentForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate employee selection
            const employeeIds = employeeSelect.getValue();
            if (employeeIds.length === 0) {
                e.preventDefault();
                alert('Please select at least one employee.');
                employeeSelect.focus();
                return false;
            }
            
            // Validate project selection
            if (!projectSelect.getValue()) {
                e.preventDefault();
                alert('Please select a project.');
                projectSelect.focus();
                return false;
            }
            
            // Validate supervisor selection
            if (!supervisorSelect.getValue()) {
                e.preventDefault();
                alert('Please select a supervisor.');
                supervisorSelect.focus();
                return false;
            }
            
            return true;
        });
    }

    // Auto-focus on project field
    setTimeout(() => {
        if (projectSelect.control_input) {
            projectSelect.focus();
        }
    }, 100);

    // Check if any dropdown is empty and disable submit button
    function checkFormValidity() {
        const hasProjects = {{ $projects->count() }} > 0;
        const hasEmployees = {{ $employees->count() }} > 0;
        const hasSupervisors = {{ $supervisors->count() }} > 0;
        
        if (submitBtn) {
            submitBtn.disabled = !(hasProjects && hasEmployees && hasSupervisors);
        }
        
        // Show warnings if needed
        if (!hasProjects) {
            console.warn('No projects available');
        }
        if (!hasEmployees) {
            console.warn('No employees available');
        }
        if (!hasSupervisors) {
            console.warn('No supervisors available');
        }
    }
    
    checkFormValidity();

    // Handle form errors
    @if($errors->any())
        // If there are form errors, make sure dropdowns show properly
        setTimeout(() => {
            employeeSelect.sync();
            projectSelect.sync();
            supervisorSelect.sync();
        }, 300);
    @endif
});
</script>
@endpush
@endsection