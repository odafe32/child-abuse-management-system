<div class="row">
    <div class="col-md-8">
        <!-- Case Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Case Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Case Number:</strong> {{ $case->case_number }}</p>
                        <p><strong>Child Name:</strong> {{ $case->child_name }}</p>
                        <p><strong>Child Age:</strong> {{ $case->child_age ?? 'Not specified' }}</p>
                        <p><strong>Date Reported:</strong> {{ $case->date_reported->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
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
                            <span class="badge {{ $statusClass }}">{{ $case->status_display }}</span>
                        </p>
                        <p><strong>Priority:</strong>
                            @php
                                $priorityClass = match($case->priority) {
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning text-dark',
                                    'high' => 'bg-danger',
                                    'critical' => 'bg-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $priorityClass }}">{{ $case->priority_display }}</span>
                        </p>
                        <p><strong>Abuse Type:</strong>
                            <span class="badge bg-info text-white">{{ $case->abuse_type_display }}</span>
                        </p>
                        <p><strong>Location:</strong> {{ $case->location ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-file-alt"></i> Description</h6>
            </div>
            <div class="card-body">
                <p>{{ $case->description ?? 'No description provided.' }}</p>
            </div>
        </div>

        <!-- Reporter Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user"></i> Reporter Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $case->reporter_name ?? 'Anonymous' }}</p>
                        <p><strong>Phone:</strong> {{ $case->reporter_phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> {{ $case->reporter_email ?? 'Not provided' }}</p>
                        <p><strong>Relationship:</strong> {{ $case->reporter_relationship ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Case Updates -->
        @if($case->updates && $case->updates->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history"></i> Case Updates</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($case->updates->sortByDesc('created_at') as $update)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="timeline-marker bg-primary"></div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $update->update_type)) }}</h6>
                                        <p class="mb-1">{{ $update->description }}</p>
                                        <small class="text-muted">
                                            by {{ $update->user->name ?? 'System' }} •
                                            {{ $update->created_at->format('M d, Y g:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Assignment Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users"></i> Assignments</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Social Worker:</strong><br>
                    @if($case->socialWorker)
                        <span class="text-success">{{ $case->socialWorker->name }}</span><br>
                        <small class="text-muted">{{ $case->socialWorker->email }}</small><br>
                        <small class="text-muted">{{ $case->socialWorker->department ?? 'No department' }}</small>
                    @else
                        <span class="text-muted">Not assigned</span>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Police Officer:</strong><br>
                    @if($case->policeOfficer)
                        <span class="text-success">{{ $case->policeOfficer->name }}</span><br>
                        <small class="text-muted">{{ $case->policeOfficer->email }}</small><br>
                        <small class="text-muted">{{ $case->policeOfficer->department ?? 'No department' }}</small>
                    @else
                        <span class="text-muted">Not assigned</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Case Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Case Statistics</h6>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong><br>
                    <small class="text-muted">{{ $case->created_at->format('M d, Y g:i A') }}</small>
                </p>
                <p><strong>Last Updated:</strong><br>
                    <small class="text-muted">{{ $case->updated_at->format('M d, Y g:i A') }}</small>
                </p>
                <p><strong>Days Since Reported:</strong><br>
                    <small class="text-muted">{{ $case->date_reported->diffInDays(now()) }} days</small>
                </p>
                @if($case->updates)
                <p><strong>Total Updates:</strong><br>
                    <small class="text-muted">{{ $case->updates->count() }} updates</small>
                </p>
                @endif
            </div>
        </div>

        <!-- Evidence Files -->
        @if($case->evidence_files)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-paperclip"></i> Evidence Files</h6>
            </div>
            <div class="card-body">
                @php
                    $evidenceFiles = json_decode($case->evidence_files, true);
                @endphp
                @if(is_array($evidenceFiles) && count($evidenceFiles) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($evidenceFiles as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file me-2"></i>
                                <span class="small">{{ basename($file) }}</span>
                            </div>
                            <a href="{{ route('admin.cases.download-evidence', ['case' => $case->id, 'filename' => basename($file)]) }}"
                               class="btn btn-sm btn-outline-primary" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0">No evidence files uploaded.</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm"
                            onclick="assignCase('{{ $case->id }}')">
                        <i class="fas fa-user-plus"></i> Assign Case
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="updateCaseStatus('{{ $case->id }}')">
                        <i class="fas fa-edit"></i> Update Status
                    </button>
                    @if($case->status !== 'closed' && $case->status !== 'resolved')
                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="addCaseNote('{{ $case->id }}')">
                        <i class="fas fa-sticky-note"></i> Add Note
                    </button>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Timeline -->
<style>
.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-top: 4px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 20px;
    bottom: -15px;
    width: 2px;
    background-color: #e3e6f0;
}

.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}
</style>

<script>
// Additional functions for quick actions
function addCaseNote(caseId) {
    // This would open a modal to add a note
    // Implementation depends on your note-adding functionality
    console.log('Add note for case:', caseId);
}

function printCase(caseId) {
    // Open case in print-friendly format
    window.open(`/admin/cases/${caseId}/print`, '_blank');
}
</script>
