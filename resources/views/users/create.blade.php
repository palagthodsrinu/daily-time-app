@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Create New User</h2>
        <p class="text-muted mb-0">Add a new user to the system</p>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Users
        </a>
    </div>
</div>

<!-- Create User Form -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-4">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Personal Information -->
                        <div class="col-12">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-user-circle me-2 text-primary"></i>Personal Information
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                   placeholder="Enter first name">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                   placeholder="Enter last name">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required
                                   placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employee Type -->
                        <div class="col-md-6">
                            <label for="employee_type" class="form-label">Employee Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_type') is-invalid @enderror" 
                                    id="employee_type" name="employee_type" required>
                                <option value="">Select Employee Type</option>
                                <option value="fulltime" {{ old('employee_type') == 'fulltime' ? 'selected' : '' }}>Full Time</option>
                                <option value="contract" {{ old('employee_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                            </select>
                            @error('employee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="doj" class="form-label">Date of Joining</label>
                            <input type="date" class="form-control @error('doj') is-invalid @enderror" 
                                   id="doj" name="doj" value="{{ old('doj') }}">
                            @error('doj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Account Settings -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-lock me-2 text-primary"></i>Account Settings
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required
                                   placeholder="Enter password">
                            <div class="form-text">Minimum 6 characters</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required
                                   placeholder="Confirm password">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Account Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="is_active">
                                    Active User
                                </label>
                            </div>
                            <div class="form-text">User will be able to login if active</div>
                        </div>
                        
                        <!-- Roles -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-semibold text-dark mb-3">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>User Roles & Permissions
                            </h5>
                            <p class="text-muted mb-3">Select the roles for this user. Users can have multiple roles.</p>
                            
                            <div class="row g-3">
                                @foreach($roles as $role)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card role-card border-0 bg-light">
                                        <div class="card-body p-3">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="role_{{ $role->id }}" name="roles[]" 
                                                       value="{{ $role->id }}"
                                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                @if($roles->count() == 0)
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No roles available. Please create roles first.
                                    </div>
                                </div>
                                @endif
                            </div>
                            @error('roles')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-user-plus me-2"></i> Create User
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
<style>
.role-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.role-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    border: 1px solid var(--primary) !important;
}

.role-card .form-check-input:checked ~ .form-check-label {
    color: var(--primary);
}

.role-card .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordFeedback = document.createElement('div');
    
    if (passwordInput) {
        passwordInput.parentNode.appendChild(passwordFeedback);
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                passwordFeedback.className = 'form-text text-muted';
                passwordFeedback.innerHTML = '<i class="fas fa-info-circle me-1"></i> Minimum 6 characters';
            } else if (password.length < 6) {
                passwordFeedback.className = 'form-text text-danger';
                passwordFeedback.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Password must be at least 6 characters';
            } else if (password.length < 8) {
                passwordFeedback.className = 'form-text text-warning';
                passwordFeedback.innerHTML = '<i class="fas fa-check-circle me-1"></i> Password strength: Fair';
            } else {
                passwordFeedback.className = 'form-text text-success';
                passwordFeedback.innerHTML = '<i class="fas fa-check-circle me-1"></i> Password strength: Strong';
            }
        });
    }
    
    // Real-time password match validation
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        passwordInput.addEventListener('input', function() {
            if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
                confirmPasswordInput.classList.add('is-invalid');
                confirmPasswordInput.classList.remove('is-valid');
            } else if (confirmPasswordInput.value) {
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            }
        });
    }
    
    // Role card click handler
    const roleCards = document.querySelectorAll('.role-card');
    roleCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.matches('input')) {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Set default date to today
    const dojInput = document.getElementById('doj');
    if (dojInput && !dojInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dojInput.value = today;
    }
});
</script>
@endpush
@endsection