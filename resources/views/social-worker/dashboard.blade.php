@extends('layouts.social-worker')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    Welcome back, {{ auth()->user()->name }}!
                    <small class="text-muted">Here's what's happening with your cases today.</small>
                </h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-primary rounded">
                                <span class="avatar-title">
                                    <i class="ri-file-list-3-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['total_cases'] }}</h5>
                            <p class="text-muted mb-0">Total Cases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-warning rounded">
                                <span class="avatar-title">
                                    <i class="ri-time-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['active_cases'] }}</h5>
                            <p class="text-muted mb-0">Active Cases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success rounded">
                                <span class="avatar-title">
                                    <i class="ri-check-double-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['completed_cases'] }}</h5>
                            <p class="text-muted mb-0">Completed Cases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-danger rounded">
                                <span class="avatar-title">
                                    <i class="ri-alarm-warning-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['overdue_cases'] }}</h5>
                            <p class="text-muted mb-0">Overdue Cases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Cases Alert -->
    @if($stats['critical_cases'] > 0 || $stats['high_priority_cases'] > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning border-warning" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ri-alert-triangle-line fs-18 me-2"></i>
                        <div>
                            <strong>Priority Cases Alert!</strong>
                            You have {{ $stats['critical_cases'] }} critical and {{ $stats['high_priority_cases'] }} high priority cases that need immediate attention.
                            <a href="{{ route('social-worker.cases.index', ['priority' => 'critical']) }}" class="alert-link">View Critical Cases</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Recent Cases -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-list-line me-1"></i>
                        Recent Cases
                    </h5>
                    <a href="{{ route('social-worker.cases.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Cases <i class="ri-arrow-right-line ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Case Number</th>
                                        <th>Child Name</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCases as $case)
                                        <tr>
                                            <td>
                                                <a href="{{ route('social-worker.cases.show', $case->id) }}" class="text-primary fw-medium">
                                                    {{ $case->case_number }}
                                                </a>
                                            </td>
                                            <td>{{ $case->child_name }}</td>
                                            <td>
                                                <span class="badge bg-info text-dark">{{ $case->abuse_type_display }}</span>
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
                                                <span class="badge {{ $priorityClass }}">{{ $case->priority_display }}</span>
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
                                                <span class="badge {{ $statusClass }}">{{ $case->status_display }}</span>
                                            </td>
                                            <td>{{ $case->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('social-worker.cases.show', $case->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-file-list-line fs-48 text-muted"></i>
                            <p class="text-muted mt-2">No cases found. Start by adding your first case.</p>
                            <a href="{{ route('social-worker.cases.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Add New Case
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Overdue Cases -->
        <div class="col-xl-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-lightning-bolt-line me-1"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('social-worker.cases.create') }}" class="btn btn-primary">
                            <i class="ri-add-circle-line me-1"></i> Add New Case
                        </a>
                        <a href="{{ route('social-worker.cases.index', ['status' => 'reported']) }}" class="btn btn-outline-warning">
                            <i class="ri-file-list-line me-1"></i> View Reported Cases
                        </a>
                        <a href="{{ route('social-worker.cases.index', ['priority' => 'critical']) }}" class="btn btn-outline-danger">
                            <i class="ri-alarm-warning-line me-1"></i> Critical Cases
                        </a>
                        <a href="{{ route('social-worker.profile') }}" class="btn btn-outline-secondary">
                            <i class="ri-user-settings-line me-1"></i> Update Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Overdue Cases -->
            @if($overdueCases->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">
                            <i class="ri-alarm-warning-line me-1"></i>
                            Overdue Cases ({{ $overdueCases->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($overdueCases as $case)
                            <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="avatar-xs bg-danger rounded-circle">
                                        <span class="avatar-title">
                                            <i class="ri-alarm-warning-line fs-12"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">
                                        <a href="{{ route('social-worker.cases.show', $case->id) }}" class="text-dark">
                                            {{ $case->case_number }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-0 small">{{ $case->child_name }}</p>
                                    <small class="text-danger">
                                        {{ $case->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('social-worker.cases.show', $case->id) }}"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        @if($overdueCases->count() >= 5)
                            <div class="text-center">
                                <a href="{{ route('social-worker.cases.index') }}" class="btn btn-sm btn-outline-danger">
                                    View All Overdue Cases
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Case Statistics Chart -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-pie-chart-line me-1"></i>
                        Case Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6">
                                <div class="mt-2">
                                    <h4 class="fw-normal text-primary">{{ $stats['active_cases'] }}</h4>
                                    <p class="text-muted mb-0 small">Active</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mt-2">
                                    <h4 class="fw-normal text-success">{{ $stats['completed_cases'] }}</h4>
                                    <p class="text-muted mb-0 small">Completed</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="mt-2">
                                    <h4 class="fw-normal text-warning">{{ $stats['high_priority_cases'] }}</h4>
                                    <p class="text-muted mb-0 small">High Priority</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mt-2">
                                    <h4 class="fw-normal text-danger">{{ $stats['critical_cases'] }}</h4>
                                    <p class="text-muted mb-0 small">Critical</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress bars for visual representation -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Case Completion Rate</small>
                            <small>{{ $stats['total_cases'] > 0 ? round(($stats['completed_cases'] / $stats['total_cases']) * 100) : 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $stats['total_cases'] > 0 ? ($stats['completed_cases'] / $stats['total_cases']) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-time-line me-1"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentCases->count() > 0)
                        <div class="timeline-alt pb-0">
                            @foreach($recentCases->take(5) as $case)
                                <div class="timeline-item">
                                    <i class="ri-file-add-line bg-primary-lighten text-primary timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mt-0 mb-1">New Case Created</h6>
                                                <p class="text-muted mb-1">
                                                    Case <strong>{{ $case->case_number }}</strong> for {{ $case->child_name }} has been registered.
                                                </p>
                                                <small class="text-muted">{{ $case->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                <a href="{{ route('social-worker.cases.show', $case->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-time-line fs-48 text-muted"></i>
                            <p class="text-muted mt-2">No recent activity to display.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tips and Guidelines -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">
                                <i class="ri-lightbulb-line text-warning me-1"></i>
                                Daily Reminders
                            </h5>
                            <ul class="mb-0 small">
                                <li>Review and update case statuses regularly</li>
                                <li>Follow up on overdue cases immediately</li>
                                <li>Ensure all case documentation is complete</li>
                                <li>Coordinate with police officers on assigned cases</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="mt-3 mt-md-0">
                                <p class="text-muted mb-1 small">Need Help?</p>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#helpModal">
                                    <i class="ri-question-line me-1"></i> View Guidelines
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">
                    <i class="ri-book-open-line me-2"></i>
                    CAMS Guidelines & Help
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Case Management</h6>
                        <ul class="small">
                            <li>Always verify reporter information</li>
                            <li>Document all interactions thoroughly</li>
                            <li>Update case status promptly</li>
                            <li>Assign priority levels appropriately</li>
                        </ul>

                        <h6 class="fw-bold mt-3">Priority Guidelines</h6>
                        <ul class="small">
                            <li><span class="badge bg-dark">Critical:</span> Immediate danger</li>
                            <li><span class="badge bg-danger">High:</span> Serious harm risk</li>
                            <li><span class="badge bg-warning text-dark">Medium:</span> Moderate concern</li>
                            <li><span class="badge bg-success">Low:</span> Minor issues</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Police Coordination</h6>
                        <ul class="small">
                            <li>Assign police for serious cases</li>
                            <li>Provide complete case information</li>
                            <li>Follow up on investigations</li>
                            <li>Maintain regular communication</li>
                        </ul>

                        <h6 class="fw-bold mt-3">Documentation</h6>
                        <ul class="small">
                            <li>Record all case updates</li>
                            <li>Include timestamps and details</li>
                            <li>Maintain confidentiality</li>
                            <li>Regular backup of records</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        // You can add AJAX call here to refresh statistics
        console.log('Dashboard auto-refresh check...');
    }, 300000); // 5 minutes

    // Show welcome message for new users
    @if($stats['total_cases'] == 0)
        setTimeout(function() {
            if (confirm('Welcome to CAMS! Would you like to add your first case now?')) {
                window.location.href = '{{ route("social-worker.cases.create") }}';
            }
        }, 2000);
    @endif
});
</script>
@endpush
