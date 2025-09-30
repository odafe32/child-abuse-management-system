@extends('layouts.police')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-warning"></i>
                Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 17 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }}
            </h1>
            <p class="text-muted mb-0">Here's what needs your attention today</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('police.assigned-cases') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-clipboard-list"></i> View All Cases
            </a>
        </div>
    </div>

    <!-- Critical Alerts -->
    @if($criticalCases > 0 || $overdueCases > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger border-left-danger" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Urgent Attention Required</h6>
                        @if($criticalCases > 0)
                            <p class="mb-1"><strong>{{ $criticalCases }}</strong> critical priority cases need immediate action</p>
                        @endif
                        @if($overdueCases > 0)
                            <p class="mb-0"><strong>{{ $overdueCases }}</strong> cases are overdue for updates</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 hover-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCases }}</div>
                            <div class="text-xs text-muted">{{ $totalCases }} total assigned</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 hover-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                High Priority
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $highPriorityCases }}</div>
                            <div class="text-xs text-muted">{{ $criticalCases }} critical</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 hover-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Under Investigation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $underInvestigation }}</div>
                            <div class="text-xs text-muted">Active investigations</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-info opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 hover-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedThisMonth }}</div>
                            <div class="text-xs text-muted">{{ $resolvedCases }} total resolved</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Priority Cases -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-fire"></i> Priority Cases Requiring Action
                    </h6>
                    <a href="{{ route('police.assigned-cases', ['priority' => 'high']) }}" class="btn btn-warning btn-sm">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($priorityCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Case</th>
                                        <th>Child</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Days</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($priorityCases as $case)
                                    <tr class="{{ $case->priority === 'critical' ? 'table-danger' : ($case->isOverdue() ? 'table-warning' : '') }}">
                                        <td>
                                            <strong class="text-primary">{{ $case->case_number }}</strong>
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
                                            <span class="badge {{ $priorityClass }}">{{ $case->priority_display }}</span>
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
                                            <span class="badge {{ $statusClass }}">{{ $case->status_display }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $case->date_reported->diffInDays(now()) }}d</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="quickUpdate('{{ $case->id }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">No Priority Cases</h6>
                            <p class="text-muted mb-0">All your high-priority cases are up to date!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('police.assigned-cases', ['status' => 'under_investigation']) }}"
                           class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-search"></i> Review Investigations ({{ $underInvestigation }})
                        </a>
                        <a href="{{ route('police.assigned-cases', ['priority' => 'critical']) }}"
                           class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-exclamation-triangle"></i> Critical Cases ({{ $criticalCases }})
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="addQuickNote()">
                            <i class="fas fa-plus"></i> Add Investigation Note
                        </button>
                        <a href="{{ route('police.cases-history') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-history"></i> View Case History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-clock"></i> Recent Activity
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($recentUpdates->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentUpdates as $update)
                            <div class="list-group-item border-0 py-2">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-2">
                                        @switch($update->update_type)
                                            @case('status_change')
                                                <i class="fas fa-exchange-alt text-primary"></i>
                                                @break
                                            @case('investigation_note')
                                                <i class="fas fa-sticky-note text-info"></i>
                                                @break
                                            @case('assignment')
                                                <i class="fas fa-user-plus text-success"></i>
                                                @break
                                            @default
                                                <i class="fas fa-info-circle text-secondary"></i>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1 small">{{ $update->case->case_number }}</h6>
                                            <small class="text-muted">{{ $update->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 small text-muted">{{ Str::limit($update->content ?? $update->description ?? 'Update', 60) }}</p>
                                        <small class="text-muted">{{ $update->case->child_name }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('police.assigned-cases') }}" class="small text-warning">
                                View All Cases <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-chart-line"></i> This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h5 font-weight-bold text-warning">{{ $newCasesThisMonth }}</div>
                                <div class="small text-muted">New Cases</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 font-weight-bold text-success">{{ $resolvedThisMonth }}</div>
                            <div class="small text-muted">Resolved</div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="text-center">
                        <div class="small text-muted mb-1">Average Resolution Time</div>
                        <div class="h6 font-weight-bold text-info">{{ $avgResolutionTime }} days</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Update Modal -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1" aria-labelledby="quickUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickUpdateModalLabel">Quick Case Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickUpdateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_status" class="form-label">Update Status</label>
                        <select class="form-select" id="quick_status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="under_investigation">Under Investigation</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_notes" class="form-label">Quick Notes</label>
                        <textarea class="form-control" id="quick_notes" name="investigation_notes"
                                  rows="3" placeholder="Brief update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function quickUpdate(caseId) {
    document.getElementById('quickUpdateForm').action = `/police/cases/${caseId}/update-status`;
    document.getElementById('quickUpdateForm').reset();
    new bootstrap.Modal(document.getElementById('quickUpdateModal')).show();
}

function addQuickNote() {
    // Redirect to assigned cases with a parameter to open the add note modal
    window.location.href = '{{ route("police.assigned-cases") }}?action=add_note';
}

// Handle quick update form submission
document.getElementById('quickUpdateForm').addEventListener('submit', function(e) {
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

// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush

@push('styles')
<style>
.hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.opacity-75 {
    opacity: 0.75;
}

.table-hover tbody tr:hover {
    background-color: rgba(246, 194, 62, 0.1);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 0.375rem;
}

.badge.bg-warning {
    color: #000 !important;
}

.badge.bg-light {
    color: #000 !important;
}

.list-group-item {
    border-left: 3px solid transparent;
    transition: border-left-color 0.2s ease-in-out;
}

.list-group-item:hover {
    border-left-color: #f6c23e;
    background-color: rgba(246, 194, 62, 0.05);
}

.border-right {
    border-right: 1px solid #e3e6f0;
}

@media (max-width: 768px) {
    .border-right {
        border-right: none;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
}
</style>
@endpush
