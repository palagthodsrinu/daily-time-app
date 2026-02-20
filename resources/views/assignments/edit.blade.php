@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Edit Assignment</h2>
        <p class="text-muted mb-0">Update project assignment details</p>
    </div>
    <div>
        <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Assignments
        </a>
    </div>
</div>

<!-- Edit Assignment Form -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('assignments.update', $assignment) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <!-- Assignment Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-link me-2 text-primary"></i>Assignment Details
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id" required>
                                <option value="">Select a project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $assignment->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} - {{ $project->client->name ?? 'No client' }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the project for this assignment</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" 
                                    id="employee_id" name="employee_id" required>
                                <option value="">Select an employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id', $assignment->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the employee to assign</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="supervisor_id" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <select class="form-select @error('supervisor_id') is-invalid @enderror" 
                                    id="supervisor_id" name="supervisor_id" required>
                                <option value="">Select a supervisor</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $assignment->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->first_name }} {{ $supervisor->last_name }} ({{ $supervisor->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the supervisor for this assignment</div>
                        </div>
                        
                        <!-- Assignment Status -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-toggle-on me-2 text-primary"></i>Assignment Status
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Assignment
                                </label>
                            </div>
                            <div class="form-text">
                                Active assignments allow employees to log time against this project
                            </div>
                        </div>
                        
                        <!-- Current Assignment Info -->
                        <div class="col-12 mt-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-semibold">Current Assignment Details</h6>
                                    <div class="row text-muted small">
                                        <div class="col-md-4">
                                            <strong>Project:</strong><br>
                                            {{ $assignment->project->name }}<br>
                                            <small>{{ $assignment->project->client->name ?? 'No client' }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Employee:</strong><br>
                                            {{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}<br>
                                            <small>{{ $assignment->employee->email }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Supervisor:</strong><br>
                                            {{ $assignment->supervisor->first_name }} {{ $assignment->supervisor->last_name }}<br>
                                            <small>{{ $assignment->supervisor->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end">
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Assignment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
              
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Enhance select elements with search
    const enhanceSelectWithSearch = (selectId, placeholder) => {
        const select = document.getElementById(selectId);
        if (select && select.options.length > 8) {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-2';
            searchInput.placeholder = placeholder;
            select.parentNode.insertBefore(searchInput, select);
            
            const originalOptions = Array.from(select.options);
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                select.innerHTML = '<option value="">' + placeholder + '</option>';
                
                originalOptions.forEach(option => {
                    if (option.value && option.text.toLowerCase().includes(searchTerm)) {
                        select.appendChild(option.cloneNode(true));
                    }
                });
            });
        }
    };
    
    // Enhance all select elements
    enhanceSelectWithSearch('project_id', 'Search projects...');
    enhanceSelectWithSearch('employee_id', 'Search employees...');
    enhanceSelectWithSearch('supervisor_id', 'Search supervisors...');
    
    // Auto-focus on first field
    const projectSelect = document.getElementById('project_id');
    if (projectSelect) {
        projectSelect.focus();
    }
});
</script>
@endpush
@endsection