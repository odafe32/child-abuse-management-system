@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-folder-open"></i> Case Management
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter Cases
            </button>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Critical Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $criticalCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">All Cases</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.cases') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search cases..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.cases') }}" class="btn btn-outline-secondary btn-sm ms-1">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($cases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="casesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Case Number</th>
                                <th>Child Name</th>
                                <th>Abuse Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Social Worker</th>
                                <th>Police Officer</th>
                                <th>Date Reported</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cases as $case)
                                <tr class="{{ $case->isOverdue() ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $case->case_number }}</strong>
                                        @if($case->isOverdue())
                                            <span class="badge bg-warning text-dark ms-1">Overdue</span>
                                        @endif
                                    </td>
                                    <td>{{ $case->child_name }}</td>
                                    <td>
                                        <span class="badge bg-info text-white">{{ $case->abuse_type_display }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityClass = match($case->priority) {
                                                'low' => 'bg-success',
                                                'medium' => 'bg-warning text-dark',
                                                'high' => 'bg-danger',
                                                'critical' => 'bg-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $priorityClass }}">
                                            {{ $case->priority_display }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($case->status) {
                                                'reported' => 'bg-info',
                                                'under_investigation' => 'bg-warning text-dark',
                                                'assigned_to_police' => 'bg-warning',
                                                'in_progress' => 'bg-secondary',
                                                'resolved' => 'bg-success',
                                                'closed' => 'bg-dark',
                                                'transferred' => 'bg-light text-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $case->status_display }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($case->socialWorker)
                                            <span class="text-muted small">{{ $case->socialWorker->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($case->policeOfficer)
                                            <span class="text-muted small">{{ $case->policeOfficer->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $case->date_reported->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="viewCase('{{ $case->id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="assignCase('{{ $case->id }}')" title="Assign Case">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="updateCaseStatus('{{ $case->id }}')" title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteCase('{{ $case->id }}')" title="Delete Case">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $cases->firstItem() }} to {{ $cases->lastItem() }} of {{ $cases->total() }} results
                    </div>
                    {{ $cases->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Cases Found</h5>
                    <p class="text-gray-500">
                        @if(request('search') || request()->hasAny(['status', 'priority', 'abuse_type']))
                            No cases match your search criteria. Try adjusting your filters.
                        @else
                            There are no cases in the system yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Case Details Modal -->
<div class="modal fade" id="caseDetailsModal" tabindex="-1" aria-labelledby="caseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseDetailsModalLabel">Case Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="caseDetailsContent">
                <!-- Case details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Assign Case Modal -->
<div class="modal fade" id="assignCaseModal" tabindex="-1" aria-labelledby="assignCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignCaseModalLabel">Assign Case</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignCaseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="social_worker_id" class="form-label">Social Worker</label>
                        <select class="form-select" id="social_worker_id" name="social_worker_id">
                            <option value="">Select Social Worker</option>
                            @foreach($socialWorkers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="police_officer_id" class="form-label">Police Officer</label>
                        <select class="form-select" id="police_officer_id" name="police_officer_id">
                            <option value="">Select Police Officer</option>
                            @foreach($policeOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Assignment Notes (Optional)</label>
                        <textarea class="form-control" id="assignment_notes" name="notes"
                                  rows="3" placeholder="Add any notes about this assignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Case</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Case Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Case Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="case_status" class="form-label">Case Status</label>
                        <select class="form-select" id="case_status" name="status" required>
                            <option value="">Select Status</option>
                            @foreach(\App\Models\CaseModel::getStatuses() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="case_priority" class="form-label">Priority</label>
                        <select class="form-select" id="case_priority" name="priority" required>
                            <option value="">Select Priority</option>
                            @foreach(\App\Models\CaseModel::getPriorities() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="status_notes" name="notes"
                                  rows="4" placeholder="Add notes about this status update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Cases</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('admin.cases') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_status" class="form-label">Status</label>
                            <select class="form-select" id="filter_status" name="status">
                                <option value="">All Statuses</option>
                                @foreach(\App\Models\CaseModel::getStatuses() as $key => $value)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_priority" class="form-label">Priority</label>
                            <select class="form-select" id="filter_priority" name="priority">
                                <option value="">All Priorities</option>
                                @foreach(\App\Models\CaseModel::getPriorities() as $key => $value)
                                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_abuse_type" class="form-label">Abuse Type</label>
                            <select class="form-select" id="filter_abuse_type" name="abuse_type">
                                <option value="">All Types</option>
                                @foreach(\App\Models\CaseModel::getAbuseTypes() as $key => $value)
                                    <option value="{{ $key }}" {{ request('abuse_type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_social_worker" class="form-label">Social Worker</label>
                            <select class="form-select" id="filter_social_worker" name="social_worker">
                                <option value="">All Social Workers</option>
                                @foreach($socialWorkers as $worker)
                                    <option value="{{ $worker->id }}" {{ request('social_worker') == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_police_officer" class="form-label">Police Officer</label>
                            <select class="form-select" id="filter_police_officer" name="police_officer">
                                <option value="">All Police Officers</option>
                                @foreach($policeOfficers as $officer)
                                    <option value="{{ $officer->id }}" {{ request('police_officer') == $officer->id ? 'selected' : '' }}>
                                        {{ $officer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="filter_date_from" name="date_from"
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="filter_date_to" name="date_to"
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('admin.cases') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCaseModal" tabindex="-1" aria-labelledby="deleteCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCaseModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteCaseForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this case? This action cannot be undone.</p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-info-circle"></i> All case data, updates, and associated records will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Case
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewCase(caseId) {
    // Load case details via AJAX
    fetch(`/admin/cases/${caseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('caseDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('caseDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading case details:', error);
            alert('Error loading case details. Please try again.');
        });
}

function assignCase(caseId) {
    // Set the form action URL
    document.getElementById('assignCaseForm').action = `/admin/cases/${caseId}/assign`;

    // Load current case data
    fetch(`/admin/cases/${caseId}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('social_worker_id').value = data.social_worker_id || '';
            document.getElementById('police_officer_id').value = data.police_officer_id || '';
            document.getElementById('assignment_notes').value = '';
            new bootstrap.Modal(document.getElementById('assignCaseModal')).show();
        })
        .catch(error => {
            console.error('Error loading case data:', error);
            alert('Error loading case data. Please try again.');
        });
}

function updateCaseStatus(caseId) {
    // Set the form action URL
    document.getElementById('updateStatusForm').action = `/admin/cases/${caseId}/status`;

    // Load current case data
    fetch(`/admin/cases/${caseId}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('case_status').value = data.status || '';
            document.getElementById('case_priority').value = data.priority || '';
            document.getElementById('status_notes').value = '';
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        })
        .catch(error => {
            console.error('Error loading case data:', error);
            alert('Error loading case data. Please try again.');
        });
}

function deleteCase(caseId) {
    // Set the form action URL
    document.getElementById('deleteCaseForm').action = `/admin/cases/${caseId}`;

    // Show the delete confirmation modal
    new bootstrap.Modal(document.getElementById('deleteCaseModal')).show();
}

// Handle form submissions with loading states
document.addEventListener('DOMContentLoaded', function() {
    // Assign case form
    document.getElementById('assignCaseForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
    });

    // Update status form
    document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    });

    // Delete case form
    document.getElementById('deleteCaseForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Print functionality
function printReport() {
    window.print();
}

// Export functionality (if needed)
function exportCases(format) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('export', format);
    window.location.href = currentUrl.toString();
}
</script>
@endpush
