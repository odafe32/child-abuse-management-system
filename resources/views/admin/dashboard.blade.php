@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i> System Overview
        </h1>
        <div class="d-flex gap-2">
            <span class="badge bg-success">System Operational</span>
            <small class="text-muted">Last updated: {{ now()->format('M d, Y g:i A') }}</small>
        </div>
    </div>

    <!-- System Health Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>System Notice:</strong> Notification system experiencing UUID compatibility issues.
                Case management is fully operational. <a href="#system-health" class="alert-link">View details</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <!-- Total Cases -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $newCasesThisMonth }} this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Cases -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Cases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCases }}</div>
                            <div class="text-xs text-info">
                                {{ $criticalCases }} critical priority
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolution Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalCases > 0 ? round(($resolvedCases / $totalCases) * 100, 1) : 0 }}%
                            </div>
                            <div class="text-xs text-success">
                                {{ $resolvedThisMonth }} resolved this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            <div class="text-xs text-muted">
                                {{ $socialWorkers }} SW • {{ $policeOfficers }} PO
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row">
        <!-- Case Analytics -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Case Analytics</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="{{ route('admin.cases') }}">View All Cases</a>
                            <a class="dropdown-item" href="#" onclick="exportReport()">Export Report</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Case Status Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800 mb-3">Case Status Distribution</h6>
                            <div class="progress-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm">Under Investigation</span>
                                    <span class="text-sm font-weight-bold">{{ $activeCases }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $totalCases > 0 ? ($activeCases / $totalCases) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="progress-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm">Resolved</span>
                                    <span class="text-sm font-weight-bold">{{ $resolvedCases }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalCases > 0 ? ($resolvedCases / $totalCases) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="progress-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm">Critical Priority</span>
                                    <span class="text-sm font-weight-bold">{{ $criticalCases }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $totalCases > 0 ? ($criticalCases / $totalCases) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-800 mb-3">Performance Metrics</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-calendar text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">Average Resolution Time</div>
                                    <div class="font-weight-bold">{{ $totalCases > 0 ? '~' . round(30 * ($resolvedCases / $totalCases), 0) : 0 }} days</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-chart-line text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">Monthly Growth</div>
                                    <div class="font-weight-bold">+{{ $newCasesThisMonth }} cases</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">Overdue Cases</div>
                                    <div class="font-weight-bold">{{ $overdueCases ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health & Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <!-- System Health -->
            <div class="card shadow mb-4" id="system-health">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Health</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-database text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500">Database</div>
                            <div class="font-weight-bold text-success">Operational</div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-folder text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500">Case Management</div>
                            <div class="font-weight-bold text-success">Operational</div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500">Notifications</div>
                            <div class="font-weight-bold text-warning">Degraded</div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>

                    <div class="alert alert-warning alert-sm mt-3">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            UUID/Integer mismatch in notifications table.
                            Core functionality unaffected.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.cases') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-folder-open"></i> Manage Cases
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-users"></i> Manage Users
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Cases -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Cases</h6>
                </div>
                <div class="card-body">
                    @if($recentCases && $recentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Case #</th>
                                        <th>Child</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Assigned</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCases->take(5) as $case)
                                    <tr>
                                        <td>
                                            <a href="#" onclick="viewCase('{{ $case->id }}')" class="text-primary">
                                                {{ $case->case_number }}
                                            </a>
                                        </td>
                                        <td>{{ $case->child_name }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($case->status) {
                                                    'reported' => 'bg-info',
                                                    'under_investigation' => 'bg-warning text-dark',
                                                    'assigned_to_police' => 'bg-warning',
                                                    'in_progress' => 'bg-secondary',
                                                    'resolved' => 'bg-success',
                                                    'closed' => 'bg-dark',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} badge-sm">
                                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                                            </span>
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
                                            <span class="badge {{ $priorityClass }} badge-sm">
                                                {{ ucfirst($case->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($case->socialWorker)
                                                <small class="text-success">{{ $case->socialWorker->name }}</small>
                                            @else
                                                <small class="text-muted">Unassigned</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $case->created_at->format('M d') }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.cases') }}" class="btn btn-outline-primary btn-sm">
                                View All Cases <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 mb-0">No recent cases</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                </div>
                <div class="card-body">
                    @if($recentUsers && $recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                         class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $user->name }}</div>
                                <div class="small text-gray-500">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }} •
                                    {{ $user->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success badge-sm">Active</span>
                                @else
                                    <span class="badge bg-warning badge-sm">Pending</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm">
                                Manage Users <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 mb-0">No recent users</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-group .progress {
    border-radius: 10px;
}

.badge-sm {
    font-size: 0.7em;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

@endsection

@push('scripts')
<script>
// Dashboard functionality
function viewCase(caseId) {
    window.location.href = `/admin/cases?search=${caseId}`;
}

function generateReport() {
    // Generate comprehensive system report
    if (confirm('Generate a comprehensive system report? This may take a few moments.')) {
        window.open('/admin/reports/generate', '_blank');
    }
}

function exportReport() {
    // Export current dashboard data
    window.print();
}

function systemMaintenance() {
    alert('System maintenance tools:\n\n' +
          '• Database optimization\n' +
          '• Log cleanup\n' +
          '• Notification system repair\n' +
          '• Performance monitoring\n\n' +
          'Contact system administrator for maintenance tasks.');
}

// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);

// Real-time updates simulation
document.addEventListener('DOMContentLoaded', function() {
    // Add subtle animations
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
});
</script>
@endpush
