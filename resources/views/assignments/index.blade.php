@extends('layouts.app')

@section('title', 'Project Assignments')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Project Assignments</h2>
        <p class="text-muted mb-0">Manage employee assignments to projects</p>
    </div>
    <div class="d-flex">
        <!-- <input type="text" class="form-control search-box me-2" placeholder="Search assignments..." id="searchInput"> -->
        <a href="{{ route('assignments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> New Assignment
        </a>
    </div>
</div>

<!-- Assignments Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">All Assignments ({{ $assignments->total() }})</h5>
        <div class="text-muted small">
            Showing {{ $assignments->firstItem() ?? 0 }}-{{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} assignments
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Assignment</th>
                    <th>Employee</th>
                    <th>Supervisor</th>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="assignment-avatar me-3">
                                <i class="fas fa-link"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">Assignment #{{ $assignment->id }}</div>
                                <small class="text-muted">
                                    {{ $assignment->project->client->name ?? 'No client' }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-small me-2">
                                <span>{{ substr($assignment->employee->first_name, 0, 1) }}{{ substr($assignment->employee->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}</div>
                                <small class="text-muted">{{ $assignment->employee->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-small me-2">
                                <span>{{ substr($assignment->supervisor->first_name, 0, 1) }}{{ substr($assignment->supervisor->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $assignment->supervisor->first_name }} {{ $assignment->supervisor->last_name }}</div>
                                <small class="text-muted">Supervisor</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="project-avatar-small me-2">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $assignment->project->name }}</div>
                                <small class="text-muted">{{ $assignment->project->client->name ?? 'No client' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $assignment->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $assignment->created_at->format('M d, Y') }}
                        </small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                        onclick="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($assignments->count() == 0)
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-link fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No assignments found</h5>
                            <p class="text-muted mb-0">Get started by creating your first project assignment</p>
                            <a href="{{ route('assignments.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> New Assignment
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if($assignments->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top gap-3">
        <div class="text-muted small">
            Showing {{ $assignments->firstItem() ?? 0 }}-{{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} assignments
        </div>
        <div class="d-flex justify-content-center justify-content-md-end w-100 w-md-auto">
            <nav aria-label="Assignments pagination">
                {{ $assignments->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.assignment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
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

/* Pagination Styles */
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

.pagination .page-item {
    margin: 1px;
}

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
        
        // Update counter
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing ${visibleCount} of ${visibleCount} assignments (filtered)`;
        }
    }
    
    function resetTable() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
        
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing {{ $assignments->firstItem() ?? 0 }}-{{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} assignments`;
        }
    }
});
</script>
@endpush
@endsection