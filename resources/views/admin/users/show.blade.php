<div class="row">
    <div class="col-md-4 text-center">
        <div class="mb-3">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="User Avatar"
                     class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                     style="width: 150px; height: 150px;">
                    <i class="fas fa-user fa-4x text-white"></i>
                </div>
            @endif
        </div>
        <h5 class="mb-1">{{ $user->name }}</h5>
        <p class="text-muted mb-3">{{ $user->email }}</p>

        @php
            $roleClass = match($user->role) {
                'admin' => 'badge-danger',
                'social_worker' => 'badge-success',
                'police' => 'badge-info',
                default => 'badge-secondary',
            };
            $roleIcon = match($user->role) {
                'admin' => 'fas fa-user-shield',
                'social_worker' => 'fas fa-user-friends',
                'police' => 'fas fa-shield-alt',
                default => 'fas fa-user',
            };
        @endphp

        <span class="badge {{ $roleClass }} fs-6 mb-3">
            <i class="{{ $roleIcon }}"></i> {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </span>

        <div class="mt-3">
            @if($user->email_verified_at)
                <span class="badge badge-success">
                    <i class="fas fa-check-circle"></i> Account Active
                </span>
            @else
                <span class="badge badge-warning">
                    <i class="fas fa-clock"></i> Pending Verification
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> User Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Full Name:</strong></div>
                    <div class="col-sm-8">{{ $user->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email Address:</strong></div>
                    <div class="col-sm-8">
                        {{ $user->email }}
                        @if($user->email_verified_at)
                            <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                        @else
                            <i class="fas fa-exclamation-triangle text-warning ms-1" title="Not Verified"></i>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Employee ID:</strong></div>
                    <div class="col-sm-8">{{ $user->employee_id }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Role:</strong></div>
                    <div class="col-sm-8">
                        <span class="badge {{ $roleClass }}">
                            <i class="{{ $roleIcon }}"></i> {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                </div>

                @if($user->department)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Department:</strong></div>
                    <div class="col-sm-8">{{ $user->department }}</div>
                </div>
                @endif

                @if($user->phone)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Phone Number:</strong></div>
                    <div class="col-sm-8">{{ $user->phone }}</div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Account Created:</strong></div>
                    <div class="col-sm-8">{{ $user->created_at->format('F d, Y \a\t g:i A') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Last Updated:</strong></div>
                    <div class="col-sm-8">{{ $user->updated_at->format('F d, Y \a\t g:i A') }}</div>
                </div>

                @if($user->email_verified_at)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email Verified:</strong></div>
                    <div class="col-sm-8">{{ $user->email_verified_at->format('F d, Y \a\t g:i A') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- User Statistics -->
        @if($user->role === 'social_worker' || $user->role === 'police')
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Case Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @if($user->role === 'social_worker')
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-primary">{{ $user->assignedCases()->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Total Cases</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-warning">{{ $user->assignedCases()->whereNotIn('status', ['resolved', 'closed'])->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Active Cases</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-success">{{ $user->assignedCases()->whereIn('status', ['resolved', 'closed'])->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Resolved Cases</div>
                        </div>
                    @elseif($user->role === 'police')
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-primary">{{ $user->policeCases()->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Assigned Cases</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-info">{{ $user->policeCases()->where('status', 'under_investigation')->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Under Investigation</div>
                        </div>
                        <div class="col-md-4">
                            <div class="h4 mb-0 font-weight-bold text-success">{{ $user->policeCases()->whereIn('status', ['resolved', 'closed'])->count() }}</div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Resolved Cases</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        @if($user->role !== 'admin')
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Recent Activity
                </h6>
            </div>
            <div class="card-body">
                @php
                    $recentUpdates = collect();
                    try {
                        if (Schema::hasTable('case_updates')) {
                            $recentUpdates = \App\Models\CaseUpdate::where('user_id', $user->id)
                                ->with('case')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        }
                    } catch (\Exception $e) {
                        // Handle if case_updates table doesn't exist
                    }
                @endphp

                @if($recentUpdates->count() > 0)
                    <div class="timeline">
                        @foreach($recentUpdates as $update)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 fs-14">
                                        @if(isset($update->update_type))
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
                                        @else
                                            <i class="fas fa-info-circle text-secondary"></i> Case Update
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $update->created_at->format('M d, H:i') }}</small>
                                </div>
                                <p class="mb-1 small">
                                    Case: <strong>{{ $update->case->case_number ?? 'N/A' }}</strong>
                                    @if(isset($update->content))
                                        <br>{{ Str::limit($update->content, 100) }}
                                    @elseif(isset($update->description))
                                        <br>{{ Str::limit($update->description, 100) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-gray-300 mb-2"></i>
                        <p class="text-muted">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-success" onclick="editUser('{{ $user->id }}')">
        <i class="fas fa-edit"></i> Edit User
    </button>
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
