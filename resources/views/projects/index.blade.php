@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Projects Management</h2>
        <p class="text-muted mb-0">Manage your organization's projects</p>
    </div>
    <div class="d-flex">
        <!-- <input type="text" class="form-control search-box me-2" placeholder="Search projects..." id="searchInput"> -->
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> New Project
        </a>
    </div>
</div>

<!-- Projects Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">All Projects ({{ $projects->total() }})</h5>
        <div class="text-muted small">
            Showing {{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} projects
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Project</th>
                    <th>Client</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="project-avatar me-3">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $project->name }}</div>
                                <small class="text-muted">ID: {{ $project->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($project->client)
                        <div class="d-flex align-items-center">
                            <div class="client-avatar-small me-2">
                                <span>{{ substr($project->client->name, 0, 1) }}</span>
                            </div>
                            <span class="fw-medium">{{ $project->client->name }}</span>
                        </div>
                        @else
                        <span class="text-muted">No client</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-muted">
                            @if($project->description)
                                {{ Str::limit($project->description, 50) }}
                            @else
                                <span class="text-muted">No description</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $project->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $project->created_at->format('M d, Y') }}
                        </small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                        onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($projects->count() == 0)
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No projects found</h5>
                            <p class="text-muted mb-0">Get started by creating your first project</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> New Project
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if($projects->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top gap-3">
        <div class="text-muted small">
            Showing {{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} projects
        </div>
        <div class="d-flex justify-content-center justify-content-md-end w-100 w-md-auto">
            <nav aria-label="Projects pagination">
                {{ $projects->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.project-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #8b5cf6, #ec4899);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.client-avatar-small {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
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
            counter.textContent = `Showing ${visibleCount} of ${visibleCount} projects (filtered)`;
        }
    }
    
    function resetTable() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
        
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing {{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} projects`;
        }
    }
});
</script>
@endpush
@endsection