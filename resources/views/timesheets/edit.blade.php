@extends('layouts.app')

@section('title', 'Edit Timesheet')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Edit Time Entry</h2>
        <p class="text-muted mb-0">Update your timesheet entry</p>
    </div>
    <div>
        <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Timesheets
        </a>
    </div>
</div>

<!-- Edit Timesheet Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('timesheets.update', $timesheet) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <!-- Entry Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-clock me-2 text-primary"></i>Time Entry Details
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                id="date" name="date" 
                                value="{{ old('date', isset($timesheet) ? $timesheet->date->format('Y-m-d') : date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}" 
                                required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id" required>
                                <option value="">Select a project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $timesheet->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ $project->client->name }})
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="hours" class="form-label">Hours <span class="text-danger">*</span></label>
                            <select class="form-select @error('hours') is-invalid @enderror" 
                                    id="hours" name="hours" required>
                                <option value="">Select hours</option>
                                @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('hours', $timesheet->hours) == $i ? 'selected' : '' }}>
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
                                <option value="0" {{ old('minutes', $timesheet->minutes) == '0' ? 'selected' : '' }}>0 minutes</option>
                                <option value="15" {{ old('minutes', $timesheet->minutes) == '15' ? 'selected' : '' }}>15 minutes</option>
                                <option value="30" {{ old('minutes', $timesheet->minutes) == '30' ? 'selected' : '' }}>30 minutes</option>
                                <option value="45" {{ old('minutes', $timesheet->minutes) == '45' ? 'selected' : '' }}>45 minutes</option>
                            </select>
                            @error('minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="4"
                                    placeholder="What did you work on? (required)"
                                    maxlength="500"
                                    required>{{ old('description', $timesheet->description) }}</textarea>
                            <div class="form-text text-end">
                                <span id="charCount">{{ strlen(old('description', $timesheet->description)) }}</span>/500 characters
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Status -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-paper-plane me-2 text-primary"></i>Submission Status
                            </h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_draft" 
                                       value="draft" {{ old('status', $timesheet->status) == 'draft' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="status_draft">
                                    Save as Draft
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_pending" 
                                       value="pending" {{ old('status', $timesheet->status) == 'pending' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="status_pending">
                                    Submit for Approval
                                </label>
                            </div>
                            <div class="form-text">
                                Draft entries can be edited later. Submitted entries will be sent for approval.
                            </div>
                            @error('status')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Current Entry Info -->
                        <div class="col-12 mt-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title fw-semibold">Current Entry</h6>
                                    <div class="row text-muted small">
                                        <div class="col-md-6">
                                            <strong>Date:</strong> {{ $timesheet->date->format('M d, Y') }}<br>
                                            <strong>Project:</strong> {{ $timesheet->project->name }}<br>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Time:</strong> {{ $timesheet->hours }}h {{ $timesheet->minutes }}m<br>
                                            <strong>Status:</strong> 
                                            <span class="badge 
                                                @if($timesheet->status === 'approved') bg-success
                                                @elseif($timesheet->status === 'pending') bg-warning text-dark
                                                @elseif($timesheet->status === 'rejected') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($timesheet->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end">
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Entry
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
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate total hours
    const hoursSelect = document.getElementById('hours');
    const minutesSelect = document.getElementById('minutes');
    
    if (hoursSelect && minutesSelect) {
        function updateTotalTime() {
            const hours = parseInt(hoursSelect.value) || 0;
            const minutes = parseInt(minutesSelect.value) || 0;
            const totalHours = hours + (minutes / 60);
            
            // You can display this somewhere if needed
            console.log('Total hours:', totalHours);
        }
        
        hoursSelect.addEventListener('change', updateTotalTime);
        minutesSelect.addEventListener('change', updateTotalTime);
    }
    
    // Character counter for description
const descriptionInput = document.getElementById('description');
const charCounter = document.getElementById('charCount');

if (descriptionInput && charCounter) {
    function updateCharCounter() {
        const length = descriptionInput.value.length;
        charCounter.textContent = length;
        
        if (length > 500) {
            charCounter.className = 'text-danger';
            descriptionInput.classList.add('is-invalid');
        } else if (length > 400) {
            charCounter.className = 'text-warning';
            descriptionInput.classList.remove('is-invalid');
        } else if (length === 0) {
            charCounter.className = 'text-danger';
            descriptionInput.classList.add('is-invalid');
        } else {
            charCounter.className = 'text-muted';
            descriptionInput.classList.remove('is-invalid');
        }
    }
    
    descriptionInput.addEventListener('input', updateCharCounter);
    updateCharCounter(); // Initial count
    
    // Validate description on form submission
    const form = document.getElementById('timesheetEntryForm') || document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const description = descriptionInput.value.trim();
            
            if (description.length === 0) {
                e.preventDefault();
                alert('Description is required.');
                descriptionInput.focus();
                descriptionInput.classList.add('is-invalid');
                return false;
            }
            
            if (description.length > 500) {
                e.preventDefault();
                alert('Description cannot exceed 500 characters.');
                descriptionInput.focus();
                return false;
            }
        });
    }
}

// Show warning if no projects are available
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    if (projectSelect && projectSelect.options.length <= 1) {
        // Create warning message
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning mt-3';
        warningDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>No projects assigned:</strong> You need to be assigned to at least one project to create timesheet entries. 
            Please contact your supervisor.
        `;
        
        // Insert after the project select or in a visible location
        projectSelect.parentNode.appendChild(warningDiv);
        
        // Disable form submission buttons
        const submitButtons = document.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            button.disabled = true;
            button.title = 'No projects available';
        });
    }
});
    // Set default date to today and restrict future dates
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
        
        if (!dateInput.value) {
            dateInput.value = today;
        }
    }
});
</script>
@endpush
@endsection