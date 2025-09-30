@extends('layouts.social-worker')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('social-worker.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cases</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    Cases Management
                    <a href="{{ route('social-worker.cases.create') }}" class="btn btn-success btn-sm ms-3">
                        <i class="mdi mdi-plus"></i> Add New Case
                    </a>
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('social-worker.cases.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Case number, child name, reporter...">
                        </div>

                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}" {{ ($filters['status'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="abuse_type" class="form-label">Abuse Type</label>
                            <select class="form-select" id="abuse_type" name="abuse_type">
                                <option value="">All Types</option>
                                @foreach($abuseTypes as $key => $value)
                                    <option value="{{ $key }}" {{ ($filters['abuse_type'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                @foreach($priorities as $key => $value)
                                    <option value="{{ $key }}" {{ ($filters['priority'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Sort By</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ ($filters['sort_by'] ?? 'created_at') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="date_reported" {{ ($filters['sort_by'] ?? '') == 'date_reported' ? 'selected' : '' }}>Date Reported</option>
                                <option value="priority" {{ ($filters['sort_by'] ?? '') == 'priority' ? 'selected' : '' }}>Priority</option>
                                <option value="status" {{ ($filters['sort_by'] ?? '') == 'status' ? 'selected' : '' }}>Status</option>
                                <option value="child_name" {{ ($filters['sort_by'] ?? '') == 'child_name' ? 'selected' : '' }}>Child Name</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="sort_order" class="form-label">Order</label>
                            <select class="form-select" id="sort_order" name="sort_order">
                                <option value="desc" {{ ($filters['sort_order'] ?? 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                                <option value="asc" {{ ($filters['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>Asc</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <a href="{{ route('social-worker.cases.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  <!-- Cases List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Case Number</th>
                                    <th>Child Name</th>
                                    <th>Abuse Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date Reported</th>
                                    <th>Police Officer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cases as $case)
                                    <tr>
                                        <td>
                                            <a href="{{ route('social-worker.cases.show', $case->id) }}" class="text-warning fw-bold">
                                                {{ $case->case_number }}
                                            </a>
                                            @if($case->isOverdue())
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td>{{ $case->child_name }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ $case->abuse_type_display }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $priorityClass = match($case->priority) {
                                                    'low' => 'bg-success',
                                                    'medium' => 'bg-warning text-dark',
                                                    'high' => 'bg-danger',
                                                    'critical' => 'bg-dark',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $priorityClass }}">
                                                {{ $case->priority_display }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($case->status) {
                                                    'reported' => 'bg-primary',
                                                    'under_investigation' => 'bg-warning text-dark',
                                                    'assigned_to_police' => 'bg-info text-dark',
                                                    'in_progress' => 'bg-secondary',
                                                    'resolved' => 'bg-success',
                                                    'closed' => 'bg-dark',
                                                    default => 'bg-light text-dark'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $case->status_display }}
                                            </span>
                                        </td>
                                        <td>{{ $case->date_reported->format('M d, Y') }}</td>
                                        <td>
                                            @if($case->policeOfficer)
                                                <span class="text-success">{{ $case->policeOfficer->name }}</span>
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- View Details -->
                                                <a href="{{ route('social-worker.cases.show', $case->id) }}"
                                                   class="btn btn-sm btn-outline-warning" title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>



                                                <!-- Edit Case (only if active) -->
                                                @if($case->isActive())
                                                    <a href="{{ route('social-worker.cases.edit', $case->id) }}"
                                                       class="btn btn-sm btn-outline-success" title="Edit Case">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                @endif

                                                <!-- Delete Case (only if reported and no police assigned) -->
                                                @if($case->status === 'reported' && !$case->police_officer_id)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Delete Case"
                                                            onclick="deleteCase('{{ $case->id }}', '{{ $case->case_number }}')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-folder-open-outline mdi-48px"></i>
                                                <p class="mt-2">No cases found matching your criteria.</p>
                                                <a href="{{ route('social-worker.cases.create') }}" class="btn btn-warning">
                                                    <i class="mdi mdi-plus"></i> Add Your First Case
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($cases->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $cases->firstItem() }} to {{ $cases->lastItem() }} of {{ $cases->total() }} results
                            </div>
                            {{ $cases->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterSelects = document.querySelectorAll('#status, #abuse_type, #priority, #sort_by, #sort_order');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Show success/error messages
    @if(session('success'))
        alert('✅ {{ session('success') }}');
    @endif

    @if(session('error'))
        alert('❌ {{ session('error') }}');
    @endif
});

// Simple delete function with window alert
function deleteCase(caseId, caseNumber) {
    if (confirm(`⚠️ Are you sure you want to delete case ${caseNumber}?\n\nThis action cannot be undone and will permanently remove:\n• All case information\n• Case timeline and updates\n• Associated notes`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/social-worker/cases/${caseId}`;
        form.submit();
    }
}
</script>
@endpush

