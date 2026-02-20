@extends('layouts.app')

@section('title', 'Users')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Users Management</h2>
        <p class="text-muted mb-0">Manage system users and their permissions</p>
    </div>
    <div class="d-flex">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Create User
        </a>
    </div>
</div>

<!-- Users Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">All Users ({{ $users->total() }})</h5>
        <div class="text-muted small">
            Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">User</th>
                    <th>Email</th>
                    <th>Employee Type</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Join Date</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                <span>{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <small class="text-muted">ID: {{ $user->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-dark">{{ $user->email }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $user->employee_type == 'fulltime' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $user->employee_type == 'fulltime' ? 'Full Time' : 'Contract' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                            @if($user->roles->count() == 0)
                            <span class="badge bg-secondary">No roles</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $user->doj ? \Carbon\Carbon::parse($user->doj)->format('M d, Y') : 'Not set' }}
                        </small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($users->count() == 0)
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No users found</h5>
                            <p class="text-muted mb-0">Get started by creating your first user</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Create User
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
  @if($users->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top gap-3">
    <div class="text-muted small">
        Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
    </div>
    <div class="d-flex justify-content-center justify-content-md-end w-100 w-md-auto">
        <nav aria-label="Users pagination">
            {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
        </nav>
    </div>
</div>
@endif
</div>
@push('styles')
<style>
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

/* Ensure proper alignment */
.pagination .page-item {
    margin: 1px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pagination {
        justify-content: center !important;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
        min-width: 36px;
    }
}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    filterTable(searchTerm);
                } else {
                    resetTable();
                }
            }
        });
    }
    
    function filterTable(searchTerm) {
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm.toLowerCase())) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing ${visibleCount} of ${visibleCount} users (filtered)`;
        }
    }
    
    function resetTable() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
        
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users`;
        }
    }
});
</script>
@endpush
@endsection