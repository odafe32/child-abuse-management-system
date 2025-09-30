@extends('layouts.police')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('police.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">My Profile</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    <div class="avatar-lg mx-auto mb-3">
                        @if($user->avatar)
                            <img  width="32" height="32" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="image" class="rounded-circle img-thumbnail avatar-lg">
                        @else
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-white fs-2 fw-bold">{{ $user->getInitials() }}</span>
                            </div>
                        @endif
                    </div>

                    <h4 class="mb-0 mt-2">{{ $user->name }}</h4>
                    <p class="text-muted fs-14">{{ $user->getRoleDisplayName() }}</p>

                    <div class="text-start mt-3">
                        <h4 class="fs-13 text-uppercase">About Me :</h4>
                        <p class="text-muted fs-13 mb-3">
                            Employee ID: <strong>{{ $user->employee_id }}</strong><br>
                            Department: <strong>{{ $user->department ?? 'Not specified' }}</strong><br>
                            Phone: <strong>{{ $user->phone ?? 'Not specified' }}</strong><br>
                            Last Login: <strong>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</strong>
                        </p>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="mdi mdi-camera me-1"></i> Change Avatar
                            </button>
                            @if($user->avatar)
                                <form action="{{ route('police.profile.remove-avatar') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to remove your avatar?')">
                                        <i class="mdi mdi-delete me-1"></i> Remove Avatar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-fill navtab-bg">
                        <li class="nav-item">
                            <a href="#profile-tab" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                <span class="d-none d-md-block">Profile Information</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#password-tab" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                <i class="mdi mdi-lock d-md-none d-block"></i>
                                <span class="d-none d-md-block">Change Password</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Profile Information Tab -->
                        <div class="tab-pane show active" id="profile-tab">
                            <form action="{{ route('police.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Personal Info</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label">Employee ID</label>
                                            <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                                                   id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" required>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <input type="text" class="form-control @error('department') is-invalid @enderror"
                                                   id="department" name="department" value="{{ old('department', $user->department) }}">
                                            @error('department')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="mdi mdi-content-save"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane" id="password-tab">
                            <form action="{{ route('police.profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-4 text-uppercase"><i class="mdi mdi-lock me-1"></i> Change Password</h5>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                                   id="current_password" name="current_password" required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control"
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="mdi mdi-lock-reset"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Change Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('police.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Choose Avatar Image</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                               id="avatar" name="avatar" accept="image/*" required>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Maximum file size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</div>
                    </div>

                    <!-- Hidden fields to maintain other data -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="employee_id" value="{{ $user->employee_id }}">
                    <input type="hidden" name="department" value="{{ $user->department }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Upload Avatar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success/error messages
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
});
</script>
@endpush
