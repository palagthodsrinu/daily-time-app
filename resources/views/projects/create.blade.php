@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Create New Project</h2>
        <p class="text-muted mb-0">Add a new project to the system</p>
    </div>
    <div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Projects
        </a>
    </div>
</div>

<!-- Create Project Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Project Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-project-diagram me-2 text-primary"></i>Project Information
                            </h5>
                        </div>
                        <div class="col-12">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Select a client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($clients->count() == 0)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No clients available. Please <a href="{{ route('clients.create') }}">create a client</a> first.
                            </div>
                            @else
                            <div class="form-text">Select the client this project belongs to</div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Enter project name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="Enter project description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Brief description about the project (optional)</div>
                        </div>
                        
                        <!-- Project Status -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-toggle-on me-2 text-primary"></i>Project Status
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Project
                                </label>
                            </div>
                            <div class="form-text">Active projects can be assigned to timesheets</div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4" {{ $clients->count() == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus me-2"></i> Create Project
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
    
    // Enhance client select with search
    const clientSelect = document.getElementById('client_id');
    if (clientSelect) {
        // Create search functionality for better UX with many clients
        const originalOptions = Array.from(clientSelect.options);
        
        // Add search input for clients if there are many
        if (originalOptions.length > 10) {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-2';
            searchInput.placeholder = 'Search clients...';
            clientSelect.parentNode.insertBefore(searchInput, clientSelect);
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                clientSelect.innerHTML = '<option value="">Select a client</option>';
                
                originalOptions.forEach(option => {
                    if (option.value && option.text.toLowerCase().includes(searchTerm)) {
                        clientSelect.appendChild(option.cloneNode(true));
                    }
                });
            });
        }
    }
});
</script>
@endpush
@endsection