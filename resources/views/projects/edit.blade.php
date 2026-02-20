@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Edit Project</h2>
        <p class="text-muted mb-0">Update project information</p>
    </div>
    <div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Projects
        </a>
    </div>
</div>

<!-- Edit Project Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <!-- Project Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-project-diagram me-2 text-primary"></i>Project Information
                            </h5>
                        </div>
                        
                        <div class="col-12">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $project->name) }}" required
                                   placeholder="Enter project name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Select a client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the client this project belongs to</div>
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4"
                                      placeholder="Enter project description (optional)">{{ old('description', $project->description) }}</textarea>
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
                                       value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Active Project
                                </label>
                            </div>
                            <div class="form-text">Active projects can be assigned to timesheets</div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end">
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Project
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
    if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
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
    
    // Enhance client select with search
    const clientSelect = document.getElementById('client_id');
    if (clientSelect) {
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