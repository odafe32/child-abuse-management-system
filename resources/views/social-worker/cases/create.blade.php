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
                        <li class="breadcrumb-item active">Add New Case</li>
                    </ol>
                </div>
                <h4 class="page-title">Add New Case</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('social-worker.cases.store') }}" method="POST" id="caseForm">
        @csrf

        <div class="row">
            <!-- Case Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            Case Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="abuse_type" class="form-label">Type of Abuse <span class="text-danger">*</span></label>
                                    <select class="form-select @error('abuse_type') is-invalid @enderror"
                                            id="abuse_type" name="abuse_type" required>
                                        <option value="">Select Abuse Type</option>
                                        @foreach($abuseTypes as $key => $value)
                                            <option value="{{ $key }}" {{ old('abuse_type') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('abuse_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority Level <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $key => $value)
                                            <option value="{{ $key }}" {{ old('priority', 'medium') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_reported" class="form-label">Date Reported <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date_reported') is-invalid @enderror"
                                           id="date_reported" name="date_reported"
                                           value="{{ old('date_reported', date('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}" required>
                                    @error('date_reported')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location of Incident <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" value="{{ old('location') }}"
                                           placeholder="Enter location where incident occurred" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Case Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Provide detailed description of the case..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 characters required</div>
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
                                <div class="mb-3">
                                    <label for="child_name" class="form-label">Child's Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('child_name') is-invalid @enderror"
                                           id="child_name" name="child_name" value="{{ old('child_name') }}"
                                           placeholder="Enter child's full name" required>
                                    @error('child_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select @error('child_gender') is-invalid @enderror"
                                            id="child_gender" name="child_gender" required>
                                        <option value="">Select Gender</option>
                                        @foreach($genders as $key => $value)
                                            <option value="{{ $key }}" {{ old('child_gender') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('child_gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('child_dob') is-invalid @enderror"
                                           id="child_dob" name="child_dob" value="{{ old('child_dob') }}"
                                           max="{{ date('Y-m-d') }}">
                                    @error('child_dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_age" class="form-label">Age (if DOB unknown)</label>
                                    <input type="number" class="form-control @error('child_age') is-invalid @enderror"
                                           id="child_age" name="child_age" value="{{ old('child_age') }}"
                                           min="0" max="18" placeholder="Enter age">
                                    @error('child_age')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="child_address" class="form-label">Child's Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('child_address') is-invalid @enderror"
                                      id="child_address" name="child_address" rows="2"
                                      placeholder="Enter child's current address" required>{{ old('child_address') }}</textarea>
                            @error('child_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_school" class="form-label">School Name</label>
                                    <input type="text" class="form-control @error('child_school') is-invalid @enderror"
                                           id="child_school" name="child_school" value="{{ old('child_school') }}"
                                           placeholder="Enter school name (if applicable)">
                                    @error('child_school')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="child_class" class="form-label">Class/Grade</label>
                                    <input type="text" class="form-control @error('child_class') is-invalid @enderror"
                                           id="child_class" name="child_class" value="{{ old('child_class') }}"
                                           placeholder="Enter class or grade">
                                    @error('child_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="medical_conditions" class="form-label">Medical Conditions</label>
                            <textarea class="form-control @error('medical_conditions') is-invalid @enderror"
                                      id="medical_conditions" name="medical_conditions" rows="2"
                                      placeholder="Any known medical conditions or special needs">{{ old('medical_conditions') }}</textarea>
                            @error('medical_conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="injuries_description" class="form-label">Injuries Description</label>
                            <textarea class="form-control @error('injuries_description') is-invalid @enderror"
                                      id="injuries_description" name="injuries_description" rows="3"
                                      placeholder="Describe any visible injuries or physical evidence">{{ old('injuries_description') }}</textarea>
                            @error('injuries_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Reporter Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-account-outline me-1"></i>
                            Reporter/Guardian Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reporter_name" class="form-label">Reporter's Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('reporter_name') is-invalid @enderror"
                                           id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}"
                                           placeholder="Enter reporter's full name" required>
                                    @error('reporter_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reporter_relationship" class="form-label">Relationship to Child <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('reporter_relationship') is-invalid @enderror"
                                           id="reporter_relationship" name="reporter_relationship" value="{{ old('reporter_relationship') }}"
                                           placeholder="e.g., Parent, Teacher, Neighbor" required>
                                    @error('reporter_relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reporter_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('reporter_phone') is-invalid @enderror"
                                           id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}"
                                           placeholder="Enter phone number" required>
                                    @error('reporter_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reporter_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('reporter_email') is-invalid @enderror"
                                           id="reporter_email" name="reporter_email" value="{{ old('reporter_email') }}"
                                           placeholder="Enter email address (optional)">
                                    @error('reporter_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reporter_address" class="form-label">Reporter's Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reporter_address') is-invalid @enderror"
                                      id="reporter_address" name="reporter_address" rows="2"
                                      placeholder="Enter reporter's contact address" required>{{ old('reporter_address') }}</textarea>
                            @error('reporter_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

<!-- Offender Information -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="mdi mdi-account-alert me-1"></i>
            Offender Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="offender_known" class="form-label">Offender Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('offender_known') is-invalid @enderror"
                            id="offender_known" name="offender_known" required>
                        <option value="">Select Status</option>
                        <option value="1" {{ old('offender_known', $case->offender_known ?? '') == '1' ? 'selected' : '' }}>
                            Known Offender
                        </option>
                        <option value="0" {{ old('offender_known', $case->offender_known ?? '') == '0' ? 'selected' : '' }}>
                            Unknown Offender
                        </option>
                    </select>
                    @error('offender_known')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6" id="offender_relationship_div">
                <div class="mb-3">
                    <label for="offender_relationship" class="form-label">Relationship to Child</label>
                    <select class="form-select @error('offender_relationship') is-invalid @enderror"
                            id="offender_relationship" name="offender_relationship">
                        <option value="">Select Relationship</option>
                        <option value="parent" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'parent' ? 'selected' : '' }}>Parent</option>
                        <option value="step_parent" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'step_parent' ? 'selected' : '' }}>Step Parent</option>
                        <option value="relative" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'relative' ? 'selected' : '' }}>Relative</option>
                        <option value="family_friend" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'family_friend' ? 'selected' : '' }}>Family Friend</option>
                        <option value="teacher" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'teacher' ? 'selected' : '' }}>Teacher/School Staff</option>
                        <option value="stranger" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'stranger' ? 'selected' : '' }}>Stranger</option>
                        <option value="other" {{ old('offender_relationship', $case->offender_relationship ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('offender_relationship')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row" id="offender_name_div">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="offender_name" class="form-label">Offender Name (if known)</label>
                    <input type="text" class="form-control @error('offender_name') is-invalid @enderror"
                           id="offender_name" name="offender_name"
                           value="{{ old('offender_name', $case->offender_name ?? '') }}"
                           placeholder="Enter offender's name if known">
                    @error('offender_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="offender_description" class="form-label">Offender Description</label>
            <textarea class="form-control @error('offender_description') is-invalid @enderror"
                      id="offender_description" name="offender_description" rows="3"
                      placeholder="Physical description, behavior, or any other relevant information about the offender">{{ old('offender_description', $case->offender_description ?? '') }}</textarea>
            @error('offender_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Case Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Case Number</label>
                            <p class="text-muted">Will be auto-generated upon submission</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Social Worker</label>
                            <p class="fw-bold">{{ auth()->user()->name }}</p>
                            <small class="text-muted">{{ auth()->user()->employee_id }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Initial Status</label>
                            <span class="badge badge-info">Reported</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Entered</label>
                            <p class="text-muted">{{ now()->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-help-circle-outline me-1"></i>
                            Guidelines
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="mdi mdi-check-circle text-success me-1"></i>
                                Ensure all required fields are completed
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-check-circle text-success me-1"></i>
                                Provide detailed and accurate information
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-check-circle text-success me-1"></i>
                                Double-check contact information
                            </li>
                            <li class="mb-2">
                                <i class="mdi mdi-check-circle text-success me-1"></i>
                                Use clear and professional language
                            </li>
                            <li>
                                <i class="mdi mdi-check-circle text-success me-1"></i>
                                Save draft frequently if needed
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-end">
                            <a href="{{ route('social-worker.cases.index') }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="mdi mdi-content-save"></i> Register Case
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('caseForm');
    const submitBtn = document.getElementById('submitBtn');

    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Registering...';
    });

    // Auto-calculate age when DOB is entered
    const dobInput = document.getElementById('child_dob');
    const ageInput = document.getElementById('child_age');

    dobInput.addEventListener('change', function() {
        if (this.value) {
            const dob = new Date(this.value);
            const today = new Date();
            const age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            ageInput.value = age >= 0 ? age : '';
        }
    });

    // Clear age when DOB is entered and vice versa
    dobInput.addEventListener('input', function() {
        if (this.value) {
            ageInput.value = '';
        }
    });

    ageInput.addEventListener('input', function() {
        if (this.value) {
            dobInput.value = '';
        }
    });

    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const descriptionCounter = document.createElement('div');
    descriptionCounter.className = 'form-text text-end';
    descriptionTextarea.parentNode.appendChild(descriptionCounter);

    function updateDescriptionCounter() {
        const length = descriptionTextarea.value.length;
        descriptionCounter.textContent = `${length} characters`;
        descriptionCounter.className = length < 10 ? 'form-text text-end text-danger' : 'form-text text-end text-muted';
    }

    descriptionTextarea.addEventListener('input', updateDescriptionCounter);
    updateDescriptionCounter();
});
document.getElementById('offender_known').addEventListener('change', function() {
    const isKnown = this.value === '1';
    const nameDiv = document.getElementById('offender_name_div');
    const relationshipDiv = document.getElementById('offender_relationship_div');

    if (isKnown) {
        nameDiv.style.display = 'block';
        relationshipDiv.style.display = 'block';
    } else {
        nameDiv.style.display = 'none';
        relationshipDiv.style.display = 'none';
        document.getElementById('offender_name').value = '';
        document.getElementById('offender_relationship').value = '';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const offenderKnown = document.getElementById('offender_known');
    if (offenderKnown.value) {
        offenderKnown.dispatchEvent(new Event('change'));
    }
});

</script>
@endpush
