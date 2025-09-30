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

                                    <a class="dropdown-item" href="{{ route('admin.profile') }}">
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
                <a href="{{ route('admin.dashboard') }}" class="logo-dark">
                    <img src="{{ url('logo.png') }}" style="height: 30px"  class="logo-sm" alt="logo sm">
                    <img src="{{ url('logo.png') }}" style="height: 60px"   class="logo-lg" alt="logo dark">
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo-light" style="margin-top: 10px">
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
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <span class="nav-icon">
                                <i class="ri-dashboard-2-line"></i>
                            </span>
                            <span class="nav-text"> Dashboard </span>
                        </a>
                    </li>

                    <!-- Add Cases -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                           href="{{ route('admin.users') }}">
                            <span class="nav-icon">
                                <i class="ri-add-circle-line"></i>
                            </span>
                            <span class="nav-text">  Users </span>
                        </a>
                    </li>


                    <!-- Profile -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.cases*') ? 'active' : '' }}"
                           href="{{ route('admin.cases') }}">
                            <span class="nav-icon">
                                 <i class="ri-contacts-book-3-line"></i>
                            </span>
                            <span class="nav-text"> Cases </span>
                        </a>
                    </li>

                    <!-- Profile -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}"
                           href="{{ route('admin.profile') }}">
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
                    <script>document.write(new Date().getFullYear())</script> &copy; CAMS - Child Abuse Management System
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
        form.action = '/admin/cases/' + caseId;
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

</script>

<!-- Page Specific Scripts -->
@stack('scripts')

</body>
</html>
