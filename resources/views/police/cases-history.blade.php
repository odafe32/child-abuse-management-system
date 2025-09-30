@extends('layouts.police')

@section('title', 'Cases History')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history"></i> Cases History
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter History
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportHistory()">
                <i class="fas fa-download"></i> Export
            </button>
            <button class="btn btn-warning btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Print
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Cases Handled
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Closed Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $closedCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Year Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $thisYearCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics Chart -->
    <div class="row mb-4">
        <div class="col-xl-12 col-lg-11">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Monthly Case Statistics ({{ now()->year }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyChart"></canvas>
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
                    <h6 class="m-0 font-weight-bold text-warning">Case History</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('police.cases-history') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search cases..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('police.cases-history') }}" class="btn btn-outline-secondary btn-sm ms-1">
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
                    <table class="table table-bordered table-hover" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>Case Number</th>
                                <th>Child Name</th>
                                <th>Abuse Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date Reported</th>
                                <th>Last Update</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cases as $case)
                                <tr>
                                    <td>
                                        <strong>{{ $case->case_number }}</strong>
                                        @if($case->isOverdue() && $case->isActive())
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
                                    <td>{{ $case->date_reported->format('M d, Y') }}</td>
                                    <td>{{ $case->updated_at->diffForHumans() }}</td>
                                    <td>
                                        @php
                                            $duration = $case->closed_at
                                                ? $case->date_reported->diffInDays($case->closed_at)
                                                : $case->date_reported->diffInDays(now());
                                        @endphp
                                        <span class="text-muted">{{ $duration }} days</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="viewCaseHistory('{{ $case->id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            @if($case->isActive())
                                                <a href="{{ route('police.assigned-cases', ['search' => $case->case_number]) }}"
                                                   class="btn btn-sm btn-outline-success" title="Go to Active Case">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
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
                    <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Case History</h5>
                    <p class="text-gray-500">You don't have any case history yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Case History Details Modal -->
<div class="modal fade" id="caseHistoryModal" tabindex="-1" aria-labelledby="caseHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseHistoryModalLabel">Case History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="caseHistoryContent">
                <!-- Case history details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Case Timeline Modal -->
<div class="modal fade" id="caseTimelineModal" tabindex="-1" aria-labelledby="caseTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseTimelineModalLabel">Case Timeline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="caseTimelineContent">
                <!-- Case timeline will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Case History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('police.cases-history') }}">
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
                            <label for="filter_year" class="form-label">Year</label>
                            <select class="form-select" id="filter_year" name="year">
                                <option value="">All Years</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="filter_date_from" name="date_from"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="filter_date_to" name="date_to"
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('police.cases-history') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-warning">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Statistics Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyData = @json($monthlyStats);
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const monthlyValues = months.map((month, index) => monthlyData[index + 1] || 0);

new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Cases',
            data: monthlyValues,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
// You can add status distribution data here if needed

function viewCaseHistory(caseId) {
    // Load case history details via AJAX
    fetch(`/police/cases/${caseId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('caseHistoryContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('caseHistoryModal')).show();
        })
        .catch(error => {
            console.error('Error loading case history:', error);
            alert('Error loading case history. Please try again.');
        });
}

function viewCaseTimeline(caseId) {
    // Load case timeline via AJAX
    fetch(`/police/cases/${caseId}/timeline`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('caseTimelineContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('caseTimelineModal')).show();
        })
        .catch(error => {
            console.error('Error loading case timeline:', error);
            alert('Error loading case timeline. Please try again.');
        });
}

function exportHistory() {
    // Export functionality
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('export', 'csv');
    window.location.href = currentUrl.toString();
}

// Initialize DataTable if available
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#historyTable').DataTable({
            "pageLength": 20,
            "order": [[ 6, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 8 }
            ]
        });
    }
});
</script>
@endpush

@push('styles')
<style>
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

.badge.bg-info,
.badge.bg-warning,
.badge.bg-success,
.badge.bg-danger,
.badge.bg-dark,
.badge.bg-secondary {
    color: #fff !important;
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

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.chart-area {
    position: relative;
    height: 320px;
}

.chart-pie {
    position: relative;
    height: 245px;
}

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
