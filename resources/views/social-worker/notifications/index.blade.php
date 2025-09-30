@extends('layouts.social-worker')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('social-worker.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="ri-notification-3-line me-2"></i>
                    Notifications
                    <button type="button" class="btn btn-primary btn-sm ms-3" id="mark-all-read-page">
                        <i class="ri-check-double-line me-1"></i> Mark All as Read
                    </button>
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
                                    <i class="ri-notification-3-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['total'] }}</h5>
                            <p class="text-muted mb-0">Total Notifications</p>
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
                                    <i class="ri-notification-badge-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['unread'] }}</h5>
                            <p class="text-muted mb-0">Unread Notifications</p>
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
                                    <i class="ri-error-warning-line fs-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['critical'] }}</h5>
                            <p class="text-muted mb-0">Critical Alerts</p>
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
                            <h5 class="mb-1">{{ $stats['total'] - $stats['unread'] }}</h5>
                            <p class="text-muted mb-0">Read Notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('social-worker.notifications.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label">Notification Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                @foreach($types as $key => $value)
                                    <option value="{{ $key }}" {{ ($filters['type'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="low" {{ ($filters['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ ($filters['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ ($filters['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ ($filters['priority'] ?? '') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="unread" {{ ($filters['status'] ?? '') == 'unread' ? 'selected' : '' }}>Unread</option>
                                <option value="read" {{ ($filters['status'] ?? '') == 'read' ? 'selected' : '' }}>Read</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-filter-line me-1"></i> Filter
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <a href="{{ route('social-worker.notifications.index') }}" class="btn btn-secondary">
                                <i class="ri-refresh-line me-1"></i> Reset Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                                <div class="notification-item d-flex align-items-start p-3 border-bottom {{ !$notification->is_read ? 'bg-light' : '' }}" data-id="{{ $notification->id }}">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-{{ $notification->priority_badge_class }} rounded">
                                            <span class="avatar-title">
                                                <i class="{{ $notification->type_icon }} fs-16"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                                    {{ $notification->title }}
                                                    @if(!$notification->is_read)
                                                        <span class="badge bg-primary ms-2">New</span>
                                                    @endif
                                                </h6>
                                                <span class="badge {{ $notification->priority_badge_class }} me-2">
                                                    {{ ucfirst($notification->priority) }}
                                                </span>
                                                <span class="badge bg-info text-dark">
                                                    {{ $notification->type_display }}
                                                </span>
                                            </div>
                                            <small class="text-muted">{{ $notification->time_ago }}</small>
                                        </div>

                                        <p class="text-muted mb-2">{{ $notification->message }}</p>

                                        <!-- Case Link -->
                                        @if($notification->data && isset($notification->data['case_id']))
                                            <div class="mb-2">
                                                <a href="{{ route('social-worker.cases.show', $notification->data['case_id']) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line me-1"></i> View Case {{ $notification->data['case_number'] ?? '' }}
                                                </a>
                                            </div>
                                        @endif

                                        <!-- Additional Data -->
                                        @if($notification->data)
                                            <div class="small text-muted">
                                                @if(isset($notification->data['child_name']))
                                                    <span class="me-3"><strong>Child:</strong> {{ $notification->data['child_name'] }}</span>
                                                @endif
                                                @if(isset($notification->data['assigned_by']))
                                                    <span class="me-3"><strong>By:</strong> {{ $notification->data['assigned_by'] }}</span>
                                                @endif
                                                @if(isset($notification->data['days_overdue']))
                                                    <span class="me-3 text-danger"><strong>Overdue:</strong> {{ $notification->data['days_overdue'] }} days</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex-shrink-0 ms-3">
                                        <div class="btn-group-vertical" role="group">
                                            @if(!$notification->is_read)
                                                <button type="button" class="btn btn-sm btn-outline-success mark-read-btn"
                                                        data-id="{{ $notification->id }}" title="Mark as Read">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-notification-btn"
                                                    data-id="{{ $notification->id }}" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($notifications->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} notifications
                                </div>
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ri-notification-off-line fs-48 text-muted"></i>
                            <h5 class="mt-3 text-muted">No Notifications Found</h5>
                            <p class="text-muted">
                                @if(array_filter($filters))
                                    No notifications match your current filters.
                                @else
                                    You don't have any notifications yet.
                                @endif
                            </p>
                            @if(array_filter($filters))
                                <a href="{{ route('social-worker.notifications.index') }}" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mark-read-btn')) {
            const btn = e.target.closest('.mark-read-btn');
            const notificationId = btn.getAttribute('data-id');
            markAsRead(notificationId, btn);
        }
    });

    // Delete notification
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-notification-btn')) {
            const btn = e.target.closest('.delete-notification-btn');
            const notificationId = btn.getAttribute('data-id');
            deleteNotification(notificationId);
        }
    });

    // Mark all as read
    document.getElementById('mark-all-read-page').addEventListener('click', function() {
        markAllAsRead();
    });

    // Auto-submit form when filters change
    const filterSelects = document.querySelectorAll('#type, #priority, #status');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    function markAsRead(notificationId, button) {
        fetch(`/social-worker/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('bg-light');
                    const badge = notificationItem.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                    const title = notificationItem.querySelector('h6');
                    if (title) title.classList.remove('fw-bold');
                    button.remove(); // Remove mark as read button
                }

                // Show success message
                showAlert('success', 'Notification marked as read');

                // Reload page after 1 second to update counts
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            showAlert('error', 'Failed to mark notification as read');
        });
    }

    function deleteNotification(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            fetch(`/social-worker/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove notification from UI
                    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.remove();
                    }

                    showAlert('success', 'Notification deleted');

                    // Reload page after 1 second to update counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error deleting notification:', error);
                showAlert('error', 'Failed to delete notification');
            });
        }
    }

    function markAllAsRead() {
        if (confirm('Are you sure you want to mark all notifications as read?')) {
            fetch('/social-worker/notifications/mark-all-read', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'All notifications marked as read');

                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
                showAlert('error', 'Failed to mark all notifications as read');
            });
        }
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            <i class="${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at the top of page content
        const pageContent = document.querySelector('.page-content');
        pageContent.insertBefore(alert, pageContent.firstChild);

        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 3000);
    }

    // Show session messages
    @if(session('success'))
        showAlert('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showAlert('error', '{{ session('error') }}');
    @endif
});
</script>
@endpush
