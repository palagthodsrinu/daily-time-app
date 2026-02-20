@extends('layouts.app')

@section('title', 'Create Client')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Add New Client</h2>
        <p class="text-muted mb-0">Create a new client for your organization</p>
    </div>
    <div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Clients
        </a>
    </div>
</div>

<!-- Create Client Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('clients.store') }}">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Client Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-building me-2 text-primary"></i>Client Information
                            </h5>
                        </div>
                        
                        <div class="col-12">
                            <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Enter client name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="Enter client description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Brief description about the client (optional)</div>
                        </div>
                        
                        <!-- Account Status -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-toggle-on me-2 text-primary"></i>Client Status
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Client
                                </label>
                            </div>
                            <div class="form-text">Active clients can be assigned to projects and timesheets</div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i> Create Client
                                </button>
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
    // Character counter for description
    const descriptionInput = document.getElementById('description');
    const charCounter = document.createElement('div');
    
    if (descriptionInput) {
        descriptionInput.parentNode.appendChild(charCounter);
        charCounter.className = 'form-text text-muted text-end';
        
        function updateCharCounter() {
            const length = descriptionInput.value.length;
            charCounter.textContent = `${length} characters`;
            
            if (length > 200) {
                charCounter.className = 'form-text text-danger text-end';
            } else if (length > 100) {
                charCounter.className = 'form-text text-warning text-end';
            } else {
                charCounter.className = 'form-text text-muted text-end';
            }
        }
        
        descriptionInput.addEventListener('input', updateCharCounter);
        updateCharCounter(); // Initial count
    }
    
    // Auto-focus on name field
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.focus();
    }
});
</script>
@endpush
@endsection