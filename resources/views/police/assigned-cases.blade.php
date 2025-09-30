@extends('layouts.police')

@section('title', 'Assigned Cases')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list"></i> My Assigned Cases
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter Cases
            </button>
            <button class="btn btn-warning btn-sm" onclick="window.print()">
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Assigned Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Under Investigation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $underInvestigation ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
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
                                High Priority Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $highPriority ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
    </div>

    <!-- Search and Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-warning">Case Management</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('police.assigned-cases') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search cases..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('police.assigned-cases') }}" class="btn btn-outline-secondary btn-sm ms-1">
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
                                <th>Date Assigned</th>
                                <th>Last Update</th>
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
                                    <td>{{ $case->created_at->format('M d, Y') }}</td>
                                    <td>{{ $case->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="viewCase('{{ $case->id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="updateCase('{{ $case->id }}')" title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="addInvestigationNote('{{ $case->id }}')" title="Add Note">
                                                <i class="fas fa-plus"></i>
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
                    <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Cases Assigned</h5>
                    <p class="text-gray-500">You don't have any cases assigned to you at the moment.</p>
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

<!-- Update Case Status Modal -->
<div class="modal fade" id="updateCaseModal" tabindex="-1" aria-labelledby="updateCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateCaseModalLabel">Update Case Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateCaseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Case Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="under_investigation">Under Investigation</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="investigation_notes" class="form-label">Investigation Notes</label>
                        <textarea class="form-control" id="investigation_notes" name="investigation_notes"
                                  rows="4" placeholder="Add your investigation findings and notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Case</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Investigation Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">Add Investigation Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNoteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note_type" class="form-label">Note Type</label>
                        <select class="form-select" id="note_type" name="note_type" required>
                            <option value="">Select Type</option>
                            <option value="investigation">Investigation Update</option>
                            <option value="evidence">Evidence Collected</option>
                            <option value="interview">Interview Conducted</option>
                            <option value="follow_up">Follow-up Action</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note_content" class="form-label">Note Content</label>
                        <textarea class="form-control" id="note_content" name="content"
                                  rows="5" placeholder="Enter detailed investigation notes..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="evidence_files" class="form-label">Attach Evidence Files (Optional)</label>
                        <input type="file" class="form-control" id="evidence_files" name="evidence_files[]"
                               multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">You can attach multiple files (PDF, DOC, Images)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Cases</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('police.assigned-cases') }}">
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
                            <label for="filter_date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="filter_date_from" name="date_from"
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="filter_date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="filter_date_to" name="date_to"
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('police.assigned-cases') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-warning">Apply Filters</button>
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
    fetch(`/police/cases/${caseId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('caseDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('caseDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading case details:', error);
            alert('Error loading case details. Please try again.');
        });
}

function updateCase(caseId) {
    // Set the form action URL
    document.getElementById('updateCaseForm').action = `/police/cases/${caseId}/update-status`;

    // Load current case data
    fetch(`/police/cases/${caseId}/data`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('status').value = data.status;
            document.getElementById('investigation_notes').value = data.investigation_notes || '';
            new bootstrap.Modal(document.getElementById('updateCaseModal')).show();
        })
        .catch(error => {
            console.error('Error loading case data:', error);
            new bootstrap.Modal(document.getElementById('updateCaseModal')).show();
        });
}

function addInvestigationNote(caseId) {
    // Set the form action URL
    document.getElementById('addNoteForm').action = `/police/cases/${caseId}/add-note`;

    // Clear form
    document.getElementById('addNoteForm').reset();

    new bootstrap.Modal(document.getElementById('addNoteModal')).show();
}

// Handle form submissions
document.getElementById('updateCaseForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating case');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating case. Please try again.');
    });
});

document.getElementById('addNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding note');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding note. Please try again.');
    });
});

// Initialize DataTable if available
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#casesTable').DataTable({
            "pageLength": 25,
            "order": [[ 5, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 7 }
            ]
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Badge styling for Bootstrap 5 compatibility */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 0.375rem;
}

/* Ensure text contrast for badges */
.badge.bg-warning {
    color: #000 !important;
}

.badge.bg-light {
    color: #000 !important;
}

.badge.bg-info,
.badge.bg-warning,
.badge.bg-success,
.badge.bg-danger,
.badge.bg-dark,
.badge.bg-secondary {
    color: #fff !important;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.btn-group .btn {
    margin-right: 2px;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.border-left-warning {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .btn-group .btn {
        margin-right: 0;
        width: 100%;
    }
}

@media print {
    .btn, .modal, .pagination {
        display: none !important;
    }
}
</style>
@endpush
