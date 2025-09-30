@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> User Management
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter Users
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus"></i> Add New User
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Social Workers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $socialWorkers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
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
                                Police Officers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $policeOfficers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
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
                                Administrators
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $admins ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">System Users</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.users') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search users..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm ms-1">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Employee ID</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="text-center">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                                 alt="Avatar" class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <span class="text-white font-weight-bold">{{ $user->getInitials() }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->phone)
                                            <br><small class="text-muted">{{ $user->phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->employee_id ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $roleClass = match($user->role) {
                                                'admin' => 'bg-danger text-white',
                                                'social_worker' => 'bg-success text-white',
                                                'police_officer' => 'bg-info text-white',
                                                default => 'bg-secondary text-white',
                                            };
                                            $roleIcon = match($user->role) {
                                                'admin' => 'fas fa-user-shield',
                                                'social_worker' => 'fas fa-user-friends',
                                                'police_officer' => 'fas fa-shield-alt',
                                                default => 'fas fa-user',
                                            };
                                        @endphp
                                        <span class="badge {{ $roleClass }}">
                                            <i class="{{ $roleIcon }}"></i> {{ $user->getRoleDisplayName() }}
                                        </span>
                                    </td>
                                    <td>{{ $user->department ?? 'N/A' }}</td>
                                    <td>
                                        @if($user->email_verified_at && $user->is_active)
                                            <span class="badge bg-success text-white">
                                                <i class="fas fa-check-circle"></i> Active
                                            </span>
                                        @elseif(!$user->email_verified_at)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white">
                                                <i class="fas fa-ban"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="viewUser('{{ $user->id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="editUser('{{ $user->id }}')" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($user->id !== auth()->id())
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteUser('{{ $user->id }}', '{{ $user->name }}')" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Users Found</h5>
                    <p class="text-gray-500">
                        @if(request('search'))
                            No users match your search criteria.
                        @else
                            Start by creating your first user.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">
                    <i class="fas fa-user-plus"></i> Create New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="create_name" name="name" required value="{{ old('name') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="create_email" name="email" required value="{{ old('email') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_employee_id" class="form-label">Employee ID *</label>
                            <input type="text" class="form-control" id="create_employee_id" name="employee_id" required value="{{ old('employee_id') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_role" class="form-label">Role *</label>
                            <select class="form-select" id="create_role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="social_worker" {{ old('role') == 'social_worker' ? 'selected' : '' }}>Social Worker</option>
                                <option value="police_officer" {{ old('role') == 'police_officer' ? 'selected' : '' }}>Police Officer</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="create_department" name="department" value="{{ old('department') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="create_phone" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="create_password" name="password" required>
                            <div class="form-text">Minimum 8 characters</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="create_avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="create_avatar" name="avatar" accept="image/*">
                        <div class="form-text">Optional. Max size: 2MB. Formats: JPG, PNG, GIF</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit"></i> Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_employee_id" class="form-label">Employee ID *</label>
                            <input type="text" class="form-control" id="edit_employee_id" name="employee_id" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role *</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Administrator</option>
                                <option value="social_worker">Social Worker</option>
                                <option value="police_officer">Police Officer</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit_department" name="department">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="edit_avatar" name="avatar" accept="image/*">
                        <div class="form-text">Leave empty to keep current picture. Max size: 2MB. Formats: JPG, PNG, GIF</div>
                        <div id="current_avatar_preview" class="mt-2"></div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_reset_password" name="reset_password">
                        <label class="form-check-label" for="edit_reset_password">
                            Reset password (user will receive email with new password)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">
                    <i class="fas fa-user"></i> User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('admin.users') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="filter_role" class="form-label">Role</label>
                        <select class="form-select" id="filter_role" name="role">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="social_worker" {{ request('role') == 'social_worker' ? 'selected' : '' }}>Social Worker</option>
                            <option value="police_officer" {{ request('role') == 'police_officer' ? 'selected' : '' }}>Police Officer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter_department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="filter_department" name="department"
                               value="{{ request('department') }}" placeholder="Enter department name">
                    </div>
                    <div class="mb-3">
                        <label for="filter_status" class="form-label">Status</label>
                        <select class="form-select" id="filter_status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewUser(userId) {
    fetch(`/admin/users/${userId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('viewUserContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewUserModal')).show();
        })
        .catch(error => {
            console.error('Error loading user details:', error);
            alert('Error loading user details. Please try again.');
        });
}

function editUser(userId) {
    fetch(`/admin/users/${userId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;

                // Populate form fields
                document.getElementById('edit_name').value = user.name || '';
                document.getElementById('edit_email').value = user.email || '';
                document.getElementById('edit_employee_id').value = user.employee_id || '';
                document.getElementById('edit_role').value = user.role || '';
                document.getElementById('edit_department').value = user.department || '';
                document.getElementById('edit_phone').value = user.phone || '';

                // Show current avatar if exists
                const avatarPreview = document.getElementById('current_avatar_preview');
                if (user.avatar) {
                    avatarPreview.innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="/storage/${user.avatar}" alt="Current Avatar" class="rounded-circle me-2" width="50" height="50">
                            <span class="text-muted">Current profile picture</span>
                        </div>
                    `;
                } else {
                    avatarPreview.innerHTML = '<span class="text-muted">No profile picture set</span>';
                }

                // Set form action
                document.getElementById('editUserForm').action = `/admin/users/${userId}`;

                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            } else {
                alert('Error loading user data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user data. Please try again.');
        });
}

function deleteUser(userId, userName) {
    if (confirm(`⚠️ Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone and will:\n• Remove the user from the system\n• Unassign them from all cases\n• Delete their profile data`)) {
        // Create form dynamically
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.style.display = 'none';

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
}

// Form validation
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('create_password').value;
    const confirmPassword = document.getElementById('create_password_confirmation').value;

    if (password !== confirmPassword) {
        e.preventDefault();
        document.getElementById('create_password_confirmation').classList.add('is-invalid');
        document.getElementById('create_password_confirmation').nextElementSibling.textContent = 'Passwords do not match';
        return false;
    }
});

// Clear validation errors when user types
document.querySelectorAll('.form-control, .form-select').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

// Show create modal if there are validation errors
@if($errors->any() && old('_token'))
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('createUserModal')).show();
    });
@endif
</script>
@endpush

@push('styles')
<style>
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

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 0.375rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fc;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.modal-lg {
    max-width: 800px;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.is-invalid {
    border-color: #e74a3b;
}

.invalid-feedback {
    display: block;
}

@media print {
    .btn, .modal, .pagination {
        display: none !important;
    }
}
</style>
@endpush
