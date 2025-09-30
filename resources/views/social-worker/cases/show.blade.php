@extends('layouts.social-worker')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('social-worker.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('social-worker.cases.index') }}">Cases</a></li>
                        <li class="breadcrumb-item active">{{ $case->case_number }}</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    Case Details: {{ $case->case_number }}
                    @if($case->isActive())
                        <a href="{{ route('social-worker.cases.edit', $case->id) }}" class="btn btn-success btn-sm ms-3">
                            <i class="mdi mdi-pencil"></i> Edit Case
                        </a>
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Case Information -->
        <div class="col-lg-8">
            <!-- Case Overview -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            Case Overview
                        </h5>
                        <div>
                            <span class="badge {{ $case->priority_badge_class }} me-2">{{ $case->priority_display }}</span>
                            <span class="badge {{ $case->status_badge_class }}">{{ $case->status_display }}</span>
                            @if($case->isOverdue())
                                <span class="badge badge-danger ms-1">Overdue</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Case Number:</td>
                                    <td>{{ $case->case_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Abuse Type:</td>
                                    <td>
                                        <span class="badge badge-info-lighten text-black">{{ $case->abuse_type_display }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date Reported:</td>
                                    <td>{{ $case->date_reported->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Location:</td>
                                    <td>{{ $case->location }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Social Worker:</td>
                                    <td>{{ $case->socialWorker->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Police Officer:</td>
                                    <td>
                                        @if($case->policeOfficer)
                                            <span class="text-success">{{ $case->policeOfficer->name }}</span>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date Entered:</td>
                                    <td>{{ $case->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $case->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6 class="fw-bold">Case Description:</h6>
                        <p class="text-muted">{{ $case->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Child Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account-child me-1"></i>
                        Child Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $case->child_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Gender:</td>
                                    <td>{{ ucfirst($case->child_gender) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Age:</td>
                                    <td>
                                        @if($case->child_dob)
                                            {{ $case->calculated_age }} years old
                                            <small class="text-muted">(DOB: {{ $case->child_dob->format('M d, Y') }})</small>
                                        @elseif($case->child_age)
                                            {{ $case->child_age }} years old
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">School:</td>
                                    <td>{{ $case->child_school ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Class/Grade:</td>
                                    <td>{{ $case->child_class ?? 'Not specified' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6 class="fw-bold">Address:</h6>
                        <p class="text-muted">{{ $case->child_address }}</p>
                    </div>

                    @if($case->medical_conditions)
                        <div class="mt-3">
                            <h6 class="fw-bold">Medical Conditions:</h6>
                            <p class="text-muted">{{ $case->medical_conditions }}</p>
                        </div>
                    @endif

                    @if($case->injuries_description)
                        <div class="mt-3">
                            <h6 class="fw-bold">Injuries Description:</h6>
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                {{ $case->injuries_description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reporter Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account-outline me-1"></i>
                        Reporter Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $case->reporter_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Relationship:</td>
                                    <td>{{ $case->reporter_relationship }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phone:</td>
                                    <td>
                                        <a href="tel:{{ $case->reporter_phone }}">{{ $case->reporter_phone }}</a>
                                    </td>
                                </tr>
                                @if($case->reporter_email)
                                    <tr>
                                        <td class="fw-bold">Email:</td>
                                        <td>
                                            <a href="mailto:{{ $case->reporter_email }}">{{ $case->reporter_email }}</a>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Address:</h6>
                            <p class="text-muted">{{ $case->reporter_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Updates/Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-timeline-outline me-1"></i>
                        Case Timeline
                    </h5>
                </div>
                <div class="card-body">
                    @if($case->updates->count() > 0)
                        <div class="timeline-alt pb-0">
                            @foreach($case->updates as $update)
                                <div class="timeline-item">
                                    <i class="mdi mdi-circle bg-{{ $update->update_type == 'case_created' ? 'info' : ($update->update_type == 'status_changed' ? 'warning' : 'primary') }}-lighten text-{{ $update->update_type == 'case_created' ? 'info' : ($update->update_type == 'status_changed' ? 'warning' : 'primary') }} timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mt-0 mb-1">{{ $update->update_type_display }}</h6>
                                                <p class="text-muted mb-1">{{ $update->description }}</p>
                                                <small class="text-muted">
                                                    by {{ $update->user->name }} • {{ $update->created_at->format('M d, Y h:i A') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="mdi mdi-timeline-outline mdi-48px text-muted"></i>
                            <p class="text-muted mt-2">No updates available for this case yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-lightning-bolt-outline me-1"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    @if($case->isActive())
                        <!-- Update Status -->
                        <button type="button" class="btn btn-primary btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="mdi mdi-update"></i> Update Status
                        </button>

                        <!-- Assign Police Officer -->
                        @if(!$case->policeOfficer)
                            <button type="button" class="btn btn-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#assignPoliceModal">
                                <i class="mdi mdi-account-plus"></i> Assign Police Officer
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#assignPoliceModal">
                                <i class="mdi mdi-account-switch"></i> Reassign Police Officer
                            </button>
                        @endif

                        <!-- Add Note -->
                        <button type="button" class="btn btn-info btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#noteModal">
                            <i class="mdi mdi-note-plus"></i> Add Note
                        </button>

                        <!-- Edit Case -->
                        <a href="{{ route('social-worker.cases.edit', $case->id) }}" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="mdi mdi-pencil"></i> Edit Case Information
                        </a>
                    @endif

                    @if($case->status === 'reported' && !$case->police_officer_id)
                        <!-- Delete Case -->
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="confirmDelete('{{ $case->id }}', '{{ $case->case_number }}')">
                            <i class="mdi mdi-delete"></i> Delete Case
                        </button>
                    @endif
                </div>
            </div>

            <!-- Case Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-line me-1"></i>
                        Case Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6">
                                <div class="mt-3">
                                    <h4 class="fw-normal text-primary">
                                        {{ $case->created_at->diffInDays(now()) }}
                                    </h4>
                                    <p class="text-muted mb-0">Days Since Created</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mt-3">
                                    <h4 class="fw-normal text-info">
                                        {{ $case->updates->count() }}
                                    </h4>
                                    <p class="text-muted mb-0">Total Updates</p>
                                </div>
                            </div>
                        </div>

                        @if($case->closed_at)
                            <div class="mt-3">
                                <h4 class="fw-normal text-success">
                                    {{ $case->created_at->diffInDays($case->closed_at) }}
                                </h4>
                                <p class="text-muted mb-0">Days to Resolution</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($case->closure_notes)
                <!-- Closure Notes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-check-circle me-1"></i>
                            Closure Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">{{ $case->closure_notes }}</p>
                        @if($case->closed_at)
                            <small class="text-muted">
                                Closed on {{ $case->closed_at->format('M d, Y h:i A') }}
                            </small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Case Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('social-worker.cases.update-status', $case->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ $case->status == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Police Officer Modal -->
<div class="modal fade" id="assignPoliceModal" tabindex="-1" aria-labelledby="assignPoliceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPoliceModalLabel">
                    {{ $case->policeOfficer ? 'Reassign' : 'Assign' }} Police Officer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('social-worker.cases.assign-police', $case->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="police_officer_id" class="form-label">Select Police Officer</label>
                        <select class="form-select" id="police_officer_id" name="police_officer_id" required>
                            <option value="">Choose Police Officer</option>
                            @foreach($policeOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ $case->police_officer_id == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->name }} ({{ $officer->employee_id }}) - {{ $officer->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Assignment Notes (Optional)</label>
                        <textarea class="form-control" id="assignment_notes" name="notes" rows="3"
                                  placeholder="Add any notes about this assignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        {{ $case->policeOfficer ? 'Reassign' : 'Assign' }} Officer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Add Note to Case</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('social-worker.cases.add-note', $case->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="4"
                                  placeholder="Enter your note here..." required></textarea>
                        <div class="form-text">Minimum 5 characters required</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Case Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert-triangle-outline me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete case <strong id="deleteCaseNumber"></strong>?</p>
                <p class="text-muted">This will permanently remove all case information and updates from the system.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete"></i> Delete Case
                    </button>
                </form>
            </div>
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

    // Auto-hide modals on form submission
    const forms = document.querySelectorAll('form[data-bs-dismiss]');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const modal = this.closest('.modal');
            if (modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    });
});

function confirmDelete(caseId, caseNumber) {
    document.getElementById('deleteCaseNumber').textContent = caseNumber;
    document.getElementById('deleteForm').action = `/social-worker/cases/${caseId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
