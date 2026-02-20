@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Edit Client</h2>
        <p class="text-muted mb-0">Update client information</p>
    </div>
    <div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Clients
        </a>
    </div>
</div>

<!-- Edit Client Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf
                    @method('PUT')
                    
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
                                   id="name" name="name" value="{{ old('name', $client->name) }}" required
                                   placeholder="Enter client name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="Enter client description (optional)">{{ old('description', $client->description) }}</textarea>
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
                                       value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Client
                                </label>
                            </div>
                            <div class="form-text">Active clients can be assigned to projects and timesheets</div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end">
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Client
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
    if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

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
        nameInput.select();
    }
});
</script>
@endpush
@endsection