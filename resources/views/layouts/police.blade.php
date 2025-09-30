<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $meta_title }}</title>

    <!-- Updated Meta Description -->
    <meta name="description" content="CAMS - Child Abuse Management System for tracking and managing child abuse cases">

    <meta name="author" content="CAMS - Child Abuse Management System" />
    <meta content="CAMS - Child Abuse Management System for tracking and managing child abuse cases" name="description" />
    <meta content="{{ $meta_title }}" property="og:title" />
    <meta content="CAMS - Child Abuse Management System for tracking and managing child abuse cases" property="og:description" />
    <meta content="{{ $meta_title }}" property="twitter:title" />
    <meta content="CAMS - Child Abuse Management System for tracking and managing child abuse cases" property="twitter:description" />
    <meta content="{{ $meta_image }}" property="og:image" />
    <meta content="{{ $meta_image }}" property="twitter:image" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:type" content="website" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="CAMS System" name="generator" />

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ url('logo.png') }}" />

    <!-- Vendor css (Require in all Page) -->
    <link href="{{ url('assets/css/vendor.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{ url('assets/css/icons.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- Remix Icons (Your existing icons) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">

    <!-- App css (Require in all Page) -->
    <link href="{{ url('assets/css/app.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

    <!-- Page Specific Styles -->
    @yield('styles')
    @stack('styles')

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{ url('assets/js/config.min.js?v=' .env('CACHE_VERSION')) }}"></script>
</head>

<body>

<!-- START Wrapper -->
<div class="wrapper">

    {{ csrf_field() }}
    <section class="h-screen flex items-center justify-center bg-no-repeat inset-0 bg-cover bg-[url('../images/bg.html')]">

        <!-- ========== Topbar Start ========== -->
        <header class="">
            <div class="topbar">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Menu Toggle Button -->
                            <div class="topbar-item">
                                <button type="button" class="button-toggle-menu topbar-button">
                                    <i class="ri-menu-2-line fs-24"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <!-- Fullscreen -->
                            <div class="dropdown topbar-item d-none d-lg-flex">
                                <button type="button" class="topbar-button" data-toggle="fullscreen">
                                    <i class="ri-fullscreen-line fs-24 fullscreen"></i>
                                    <i class="ri-fullscreen-exit-line fs-24 quit-fullscreen"></i>
                                </button>
                            </div>

                            <!-- Notification -->
                            {{-- <div class="dropdown topbar-item">
                                <button type="button" class="topbar-button position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-notification-3-line fs-24"></i>
                                    <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill" id="notification-count" style="display: none;">
                                        0<span class="visually-hidden">unread messages</span>
                                    </span>
                                </button>
                                <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" aria-labelledby="page-header-notifications-dropdown">
                                    <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="mark-all-read">
                                                    <small>Mark All Read</small>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-simplebar style="max-height: 350px;" id="notifications-container">
                                        <div class="text-center py-4" id="notifications-loading">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2 mb-0">Loading notifications...</p>
                                        </div>
                                    </div>
                                    <div class="text-center py-3 border-top">
                                        <a href="{{ route('police.notifications') }}" class="btn btn-primary btn-sm">
                                            View All Notifications <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- User -->
                            <div class="dropdown topbar-item">
                                <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-flex align-items-center">
                                        @if(auth()->user()->avatar)
                                            <img class="rounded-circle" width="32" height="32" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user-avatar">
                                        @else
                                            <img class="rounded-circle" width="32" src="{{ url('empty.webp') }}" alt="default-avatar">
                                        @endif
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <h6 class="dropdown-header">Welcome {{ auth()->user()->name ?? 'User' }}!</h6>

                                    <a class="dropdown-item" href="{{ route('police.profile') }}">
                                        <i class="ri-user-line me-2"></i> <span class="align-middle">Profile</span>
                                    </a>

                                    <div class="dropdown-divider my-1"></div>

                                    <form action="{{ route('logout') }}" method="POST" style="display: inline; cursor: pointer;">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="ri-logout-circle-line me-2"></i><span class="align-middle">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        <div class="main-nav">
            <!-- Sidebar Logo -->
            <div class="logo-box">
                <a href="{{ route('police.dashboard') }}" class="logo-dark">
                    <img src="{{ url('logo.png') }}" style="height: 30px"  class="logo-sm" alt="logo sm">
                    <img src="{{ url('logo.png') }}" style="height: 60px"   class="logo-lg" alt="logo dark">
                </a>

                <a href="{{ route('police.dashboard') }}" class="logo-light" style="margin-top: 10px">
                    <img src="{{ url('logo.png') }}" style="height: 30px" class="logo-sm" alt="logo sm">
                    <img src="{{ url('logo.png') }}" style="height: 60px" class="logo-lg" alt="logo light">
                </a>
            </div>

            <!-- Menu Toggle Button (sm-hover) -->
            <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
                <i class="ri-menu-2-line fs-24 button-sm-hover-icon"></i>
            </button>

            <div class="scrollbar" data-simplebar>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title">Menu</li>

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('police.dashboard') ? 'active' : '' }}"
                           href="{{ route('police.dashboard') }}">
                            <span class="nav-icon">
                                <i class="ri-dashboard-2-line"></i>
                            </span>
                            <span class="nav-text"> Dashboard </span>
                        </a>
                    </li>

                    <!-- Assigned Cases -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('police.assigned-cases') ? 'active' : '' }}"
                           href="{{ route('police.assigned-cases') }}">
                            <span class="nav-icon">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                            <span class="nav-text"> Assigned Cases </span>
                        </a>
                    </li>

                    <!-- Cases History -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('police.cases-history*') ? 'active' : '' }}"
                           href="{{ route('police.cases-history') }}">
                            <span class="nav-icon">
                                <i class="ri-history-line"></i>
                            </span>
                            <span class="nav-text"> Cases History </span>
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('police.notifications*') ? 'active' : '' }}"
                           href="{{ route('police.notifications') }}">
                            <span class="nav-icon">
                                <i class="ri-notification-3-line"></i>
                            </span>
                            <span class="nav-text"> Notifications </span>
                            <span class="position-absolute top-50 end-0 translate-middle-y badge bg-danger rounded-pill"
                                  id="sidebar-notification-count" style="display: none; font-size: 0.6rem; margin-right: 10px;">
                                0
                            </span>
                        </a>
                    </li>

                    <!-- Profile -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('police.profile*') ? 'active' : '' }}"
                           href="{{ route('police.profile') }}">
                            <span class="nav-icon">
                                <i class="ri-user-line"></i>
                            </span>
                            <span class="nav-text"> Profile </span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
        <!-- ========== App Menu End ========== -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            <!-- Display Success Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Display Error Messages -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Display Status Messages -->
            @if (session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ri-information-line me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
        <!-- End Content -->
    </section>

    <!-- ========== Footer Start ========== -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <script>document.write(new Date().getFullYear())</script> &copy; CAMS - Child Abuse Management System . KAGAYAKI
                </div>
            </div>
        </div>
    </footer>
    <!-- ========== Footer End ========== -->

</div>
<!-- ==================================================== -->
<!-- End Page Content -->
<!-- ==================================================== -->

</div>

<!-- Vendor Javascript (Require in all Page) -->
<script src="{{ url('assets/js/vendor.js?v=' .env('CACHE_VERSION')) }}"></script>

<!-- App Javascript (Require in all Page) -->
<script src="{{ url('assets/js/app.js?v=' .env('CACHE_VERSION')) }}"></script>

<!-- Global Delete Function -->
<script>
// Global delete function that works everywhere
window.deleteCase = function(caseId, caseNumber) {
    if (confirm('⚠️ Are you sure you want to delete case ' + caseNumber + '?\n\nThis action cannot be undone and will permanently remove:\n• All case information\n• Case timeline and updates\n• Associated notes')) {
        // Create form dynamically
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/police/cases/' + caseId;
        form.style.display = 'none';

        // Add CSRF token
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Add method spoofing for DELETE
        var methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
};

// Police Notification System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.getElementById('page-header-notifications-dropdown');
    const notificationCount = document.getElementById('notification-count');
    const sidebarNotificationCount = document.getElementById('sidebar-notification-count');
    const notificationsContainer = document.getElementById('notifications-container');
    const notificationsLoading = document.getElementById('notifications-loading');
    const markAllReadBtn = document.getElementById('mark-all-read');

    // Load notifications when dropdown is opened
    notificationDropdown.addEventListener('click', function() {
        loadNotifications();
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        markAllAsRead();
    });

    // Load notifications function
    function loadNotifications() {
        notificationsLoading.style.display = 'block';

        fetch('{{ route("police.notifications.api.get") }}')
            .then(response => response.json())
            .then(data => {
                notificationsLoading.style.display = 'none';
                displayNotifications(data.notifications);
                updateNotificationCount(data.unread_count);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsLoading.style.display = 'none';
                notificationsContainer.innerHTML = '<div class="text-center py-4"><p class="text-muted">Error loading notifications</p></div>';
            });
    }

    // Display notifications
    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            notificationsContainer.innerHTML = '<div class="text-center py-4"><i class="ri-notification-off-line fs-48 text-muted"></i><p class="text-muted mt-2">No notifications</p></div>';
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const isUnread = !notification.is_read;
            const timeAgo = formatTimeAgo(notification.created_at);

            html += `
                <div class="dropdown-item py-3 border-bottom notification-item ${isUnread ? 'bg-light' : ''}" data-id="${notification.id}">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-${getPriorityColor(notification.priority)} rounded">
                                <span class="avatar-title">
                                    <i class="${getTypeIcon(notification.type)} fs-16"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1 fs-14 ${isUnread ? 'fw-bold' : ''}">${notification.title}</h6>
                                ${isUnread ? '<span class="badge bg-primary rounded-pill">New</span>' : ''}
                            </div>
                            <p class="text-muted mb-1 fs-13">${notification.message}</p>
                            <small class="text-muted">${timeAgo}</small>
                            ${notification.data && notification.data.case_id ?
                                `<div class="mt-1">
                                    <a href="/police/cases/${notification.data.case_id}" class="btn btn-sm btn-outline-primary">View Case</a>
                                </div>` : ''
                            }
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="${notification.id}" title="Mark as read">
                                <i class="ri-check-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        notificationsContainer.innerHTML = html;

        // Add click handlers for mark as read buttons
        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const notificationId = this.getAttribute('data-id');
                markAsRead(notificationId);
            });
        });
    }

    // Mark single notification as read
    function markAsRead(notificationId) {
        fetch(`/police/notifications/${notificationId}/read`, {
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
                    const badge = notificationItem.querySelector('.badge');
                    if (badge) badge.remove();
                    const title = notificationItem.querySelector('h6');
                    if (title) title.classList.remove('fw-bold');
                }
                updateNotificationCount();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    // Mark all as read
    function markAllAsRead() {
        fetch('{{ route("police.notifications.mark-all-read") }}', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload notifications
                updateNotificationCount(0);
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    // Update notification count
    function updateNotificationCount(count = null) {
        if (count === null) {
            fetch('{{ route("police.notifications.api.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    updateCountDisplay(data.unread_count);
                });
        } else {
            updateCountDisplay(count);
        }
    }

    function updateCountDisplay(count) {
        // Update topbar notification count
        if (count > 0) {
            notificationCount.textContent = count;
            notificationCount.style.display = 'block';
        } else {
            notificationCount.style.display = 'none';
        }

        // Update sidebar notification count
        if (sidebarNotificationCount) {
            if (count > 0) {
                sidebarNotificationCount.textContent = count;
                sidebarNotificationCount.style.display = 'block';
            } else {
                sidebarNotificationCount.style.display = 'none';
            }
        }
    }

    // Helper functions
    function getPriorityColor(priority) {
        const colors = {
            'low': 'success',
            'medium': 'warning',
            'high': 'danger',
            'critical': 'dark'
        };
        return colors[priority] || 'secondary';
    }

    function getTypeIcon(type) {
        const icons = {
            'case_assigned': 'ri-file-add-line',
            'case_updated': 'ri-file-edit-line',
            'case_overdue': 'ri-alarm-warning-line',
            'case_critical': 'ri-error-warning-line',
            'police_assigned': 'ri-shield-user-line',
            'case_resolved': 'ri-check-double-line',
            'system_alert': 'ri-notification-3-line'
        };
        return icons[type] || 'ri-information-line';
    }

    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }

    // Load initial notification count
    updateNotificationCount();

    // Auto-refresh notifications every 30 seconds
    setInterval(updateNotificationCount, 30000);

    // Show alerts for session messages
    @if(session('success'))
        console.log('✅ {{ session('success') }}');
    @endif

    @if(session('error'))
        console.log('❌ {{ session('error') }}');
    @endif
});
</script>

<!-- Page Specific Scripts -->
@stack('scripts')

</body>
</html>
