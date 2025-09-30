<div class="row">
    <!-- Case Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Case Information</h6>
                <div>
                    <span class="badge {{ $case->status_badge_class }}">{{ $case->status_display }}</span>
                    <span class="badge {{ $case->priority_badge_class }} ms-1">{{ $case->priority_display }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Case Number:</strong></td>
                                <td>{{ $case->case_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date Reported:</strong></td>
                                <td>{{ $case->date_reported->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Abuse Type:</strong></td>
                                <td>{{ $case->abuse_type_display }}</td>
                            </tr>
                            <tr>
                                <td><strong>Location:</strong></td>
                                <td>{{ $case->location }}</td>
                            </tr>
                            <tr>
                                <td><strong>Social Worker:</strong></td>
                                <td>{{ $case->socialWorker->name ?? 'Not Assigned' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Priority:</strong></td>
                                <td>{{ $case->priority_display }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>{{ $case->status_display }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $case->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $case->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @if($case->closed_at)
                            <tr>
                                <td><strong>Closed:</strong></td>
                                <td>{{ $case->closed_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($case->description)
                <div class="mt-3">
                    <h6><strong>Case Description:</strong></h6>
                    <p class="text-muted">{{ $case->description }}</p>
                </div>
                @endif

                @if($case->investigation_notes)
                <div class="mt-3">
                    <h6><strong>Investigation Notes:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        {{ $case->investigation_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Child Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Child Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $case->child_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Age:</strong></td>
                                <td>{{ $case->calculated_age }} years old</td>
                            </tr>
                            <tr>
                                <td><strong>Gender:</strong></td>
                                <td>{{ ucfirst($case->child_gender) }}</td>
                            </tr>
                            @if($case->child_dob)
                            <tr>
                                <td><strong>Date of Birth:</strong></td>
                                <td>{{ $case->child_dob->format('M d, Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            @if($case->child_school)
                            <tr>
                                <td><strong>School:</strong></td>
                                <td>{{ $case->child_school }}</td>
                            </tr>
                            @endif
                            @if($case->child_class)
                            <tr>
                                <td><strong>Class:</strong></td>
                                <td>{{ $case->child_class }}</td>
                            </tr>
                            @endif
                            @if($case->medical_conditions)
                            <tr>
                                <td><strong>Medical Conditions:</strong></td>
                                <td>{{ $case->medical_conditions }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($case->child_address)
                <div class="mt-3">
                    <h6><strong>Address:</strong></h6>
                    <p class="text-muted">{{ $case->child_address }}</p>
                </div>
                @endif

                @if($case->injuries_description)
                <div class="mt-3">
                    <h6><strong>Injuries Description:</strong></h6>
                    <div class="bg-warning bg-opacity-10 p-3 rounded border-start border-warning border-3">
                        {{ $case->injuries_description }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Reporter Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Reporter Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $case->reporter_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Relationship:</strong></td>
                                <td>{{ $case->reporter_relationship }}</td>
                            </tr>
                            @if($case->reporter_phone)
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $case->reporter_phone }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($case->reporter_email)
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $case->reporter_email }}</td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>

                @if($case->reporter_address)
                <div class="mt-3">
                    <h6><strong>Reporter Address:</strong></h6>
                    <p class="text-muted">{{ $case->reporter_address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Offender Information -->
        @if($case->offender_known || $case->offender_name || $case->offender_description)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Offender Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge {{ $case->offender_known ? 'badge-warning' : 'badge-secondary' }}">
                                        {{ $case->offender_status }}
                                    </span>
                                </td>
                            </tr>
                            @if($case->offender_name)
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $case->offender_name }}</td>
                            </tr>
                            @endif
                            @if($case->offender_relationship)
                            <tr>
                                <td><strong>Relationship to Child:</strong></td>
                                <td>{{ $case->offender_relationship }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($case->offender_description)
                <div class="mt-3">
                    <h6><strong>Description:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        {{ $case->offender_description }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Case Updates & Timeline -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Case Timeline</h6>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @if($case->updates->count() > 0)
                    <div class="timeline">
                        @foreach($case->updates as $update)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">
                                        @switch($update->update_type)
                                            @case('status_change')
                                                <i class="fas fa-exchange-alt text-primary"></i> Status Update
                                                @break
                                            @case('investigation_note')
                                                <i class="fas fa-sticky-note text-info"></i> Investigation Note
                                                @break
                                            @case('assignment')
                                                <i class="fas fa-user-plus text-success"></i> Assignment
                                                @break
                                            @default
                                                <i class="fas fa-info-circle text-secondary"></i> Update
                                        @endswitch
                                    </h6>
                                    <small class="text-muted">{{ $update->created_at->format('M d, H:i') }}</small>
                                </div>
                                <p class="mb-1 small">{{ $update->content }}</p>
                                <small class="text-muted">
                                    by {{ $update->user->name ?? 'System' }}
                                    @if($update->user && $update->user->role)
                                        ({{ ucfirst($update->user->role) }})
                                    @endif
                                </small>

                                @if($update->metadata)
                                    @php $metadata = json_decode($update->metadata, true); @endphp
                                    @if(isset($metadata['evidence_files']) && count($metadata['evidence_files']) > 0)
                                        <div class="mt-2">
                                            <small class="text-muted">Evidence Files:</small>
                                            @foreach($metadata['evidence_files'] as $file)
                                                <div class="small">
                                                    <i class="fas fa-paperclip"></i>
                                                    <a href="{{ route('police.download-evidence', [$case->id, basename($file['path'])]) }}"
                                                       class="text-decoration-none">
                                                        {{ $file['original_name'] }}
                                                    </a>
                                                    <span class="text-muted">({{ number_format($file['size'] / 1024, 1) }} KB)</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p>No updates yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -16px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 12px;
    border-radius: 8px;
    border-left: 3px solid #4e73df;
}
</style>
