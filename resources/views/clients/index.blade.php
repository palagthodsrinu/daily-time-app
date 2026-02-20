@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Clients Management</h2>
        <p class="text-muted mb-0">Manage your organization's clients</p>
    </div>
    <div class="d-flex">
        <!-- <input type="text" class="form-control search-box me-2" placeholder="Search clients..." id="searchInput"> -->
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add Client
        </a>
    </div>
</div>

<!-- Clients Table -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="fw-bold mb-0 text-dark">All Clients ({{ $clients->total() }})</h5>
        <div class="text-muted small">
            Showing {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Client</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="client-avatar me-3">
                                <span>{{ substr($client->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $client->name }}</div>
                                <small class="text-muted">ID: {{ $client->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">
                            @if($client->description)
                                {{ Str::limit($client->description, 50) }}
                            @else
                                <span class="text-muted">No description</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $client->created_at->format('M d, Y') }}
                        </small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center" 
                                        onclick="return confirm('Are you sure you want to delete this client? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($clients->count() == 0)
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No clients found</h5>
                            <p class="text-muted mb-0">Get started by adding your first client</p>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Add Client
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if($clients->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top gap-3">
        <div class="text-muted small">
            Showing {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients
        </div>
        <div class="d-flex justify-content-center justify-content-md-end w-100 w-md-auto">
            <nav aria-label="Clients pagination">
                {{ $clients->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.client-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
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
            counter.textContent = `Showing ${visibleCount} of ${visibleCount} clients (filtered)`;
        }
    }
    
    function resetTable() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => row.style.display = '');
        
        const counter = document.querySelector('.table-container .text-muted.small');
        if (counter) {
            counter.textContent = `Showing {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients`;
        }
    }
});
</script>
@endpush
@endsection