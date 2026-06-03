@extends('layouts.dashboard')

@section('title', 'Assign Inquiry to Agency - MCMC')

@push('styles')
<style>
.inquiry-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.agency-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.agency-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.agency-card.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.assignment-form {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.existing-assignment {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.form-floating {
    margin-bottom: 1rem;
}

.btn-assign {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-assign:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('mcmc.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mcmc.inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item active">Assign to Agency</li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Assign Inquiry to Agency</h1>
                    <p class="text-muted">Select an appropriate agency to handle this inquiry</p>
                </div>
                <div>
                    <a href="{{ route('mcmc.inquiries.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inquiries
                    </a>
                </div>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Inquiry Details -->
            <div class="inquiry-details">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-3">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            Inquiry Details
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Inquiry ID:</strong>
                                <span class="ms-2">#{{ $inquiry->inquiry_ID }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong>
                                <span class="ms-2">
                                    <span class="status-badge bg-{{ $inquiry->inquiry_Status == 'pending' ? 'warning' : 'info' }}">
                                        {{ $inquiry->formatted_status }}
                                    </span>
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Category:</strong>
                                <span class="ms-2">{{ $inquiry->formatted_inquiry_type }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Submitted:</strong>
                                <span class="ms-2">{{ $inquiry->inquiry_Created_At->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong>Subject:</strong>
                                <p class="ms-2 mb-0">{{ $inquiry->inquiry_Title }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Submitted By</h5>
                        <div class="bg-white p-3 rounded">
                            <div><strong>{{ $inquiry->user->name ?? 'N/A' }}</strong></div>
                            <div class="text-muted">{{ $inquiry->user->email ?? 'N/A' }}</div>
                            @if($inquiry->user->phone ?? false)
                                <div class="text-muted">{{ $inquiry->user->phone }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Description:</strong>
                        <div class="bg-white p-3 rounded mt-2">
                            {{ $inquiry->inquiry_Description }}
                        </div>
                    </div>
                </div>

                @if($inquiry->hasAttachment())
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Attachment:</strong>
                            <div class="mt-2">
                                <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download"></i> Download {{ $inquiry->attachment_filename }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Existing Assignment Warning -->
            @if($existingAssignment)
                <div class="existing-assignment">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <strong>Existing Assignment</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Current Agency:</strong><br>
                            {{ $existingAssignment->agency->agency_Name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong><br>
                            <span class="status-badge bg-{{ $existingAssignment->assignment_Status == 'pending' ? 'warning' : 'info' }}">
                                {{ ucfirst($existingAssignment->assignment_Status) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Assigned On:</strong><br>
                            {{ $existingAssignment->assignment_Date->format('M d, Y H:i') }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            Proceeding will reassign this inquiry to a new agency.
                        </small>
                    </div>
                </div>
            @endif

            <!-- Assignment Form -->
            <div class="assignment-form">
                <h4 class="mb-4">
                    <i class="fas fa-building text-primary me-2"></i>
                    {{ $existingAssignment ? 'Reassign to Agency' : 'Assign to Agency' }}
                </h4>

                <form method="POST" action="{{ route('mcmc.inquiries.assign', $inquiry->inquiry_ID) }}" id="assignmentForm">
                    @csrf

                    @if($existingAssignment)
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="reassignment_reason" name="reassignment_reason"
                                      style="height: 100px" placeholder="Reason for reassignment" required>{{ old('reassignment_reason') }}</textarea>
                            <label for="reassignment_reason">Reason for Reassignment *</label>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label">
                            <strong>Select Agency *</strong>
                            <small class="text-muted">(Click on an agency card to select)</small>
                        </label>

                        <div class="row">
                            @foreach($agencies as $agency)
                                <div class="col-md-6 col-lg-4">
                                    <div class="agency-card" data-agency-id="{{ $agency->agency_ID }}">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="agency_id"
                                                   id="agency_{{ $agency->agency_ID }}" value="{{ $agency->agency_ID }}"
                                                   {{ old('agency_id') == $agency->agency_ID ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="agency_{{ $agency->agency_ID }}">
                                                <div>
                                                    <strong>{{ $agency->agency_Name }}</strong>
                                                </div>
                                                <div class="text-muted small">{{ $agency->formatted_agency_type }}</div>
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-envelope"></i> {{ $agency->agency_Email }}
                                                </div>
                                                @if($agency->agency_Phone)
                                                    <div class="text-muted small">
                                                        <i class="fas fa-phone"></i> {{ $agency->agency_Phone }}
                                                    </div>
                                                @endif
                                                <div class="mt-2">
                                                    <span class="badge bg-info">
                                                        Pending: {{ $agency->getPendingAssignmentsCount() }}
                                                    </span>
                                                    <span class="badge bg-success">
                                                        Completed: {{ $agency->getCompletedAssignmentsCount() }}
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="comments" name="comments"
                                  style="height: 120px" placeholder="Assignment comments">{{ old('comments') }}</textarea>
                        <label for="comments">Assignment Comments & Instructions</label>
                        <div class="form-text">Provide any specific instructions or context for the agency.</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-assign text-white" id="assignButton" disabled>
                                <i class="fas fa-share-alt"></i>
                                {{ $existingAssignment ? 'Reassign Inquiry' : 'Assign to Agency' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to assign this inquiry to <strong id="selectedAgencyName"></strong>?</p>
                <div id="reassignmentWarning" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    This will reassign the inquiry from the current agency.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">
                    Confirm Assignment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const agencyCards = document.querySelectorAll('.agency-card');
    const agencyRadios = document.querySelectorAll('input[name="agency_id"]');
    const assignButton = document.getElementById('assignButton');
    const form = document.getElementById('assignmentForm');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const hasExistingAssignment = {{ $existingAssignment ? 'true' : 'false' }};

    // Handle agency card clicks
    agencyCards.forEach(card => {
        card.addEventListener('click', function() {
            const agencyId = this.dataset.agencyId;
            const radio = document.getElementById(`agency_${agencyId}`);

            // Uncheck all other radios and remove selected class
            agencyRadios.forEach(r => r.checked = false);
            agencyCards.forEach(c => c.classList.remove('selected'));

            // Check this radio and add selected class
            radio.checked = true;
            this.classList.add('selected');

            updateAssignButton();
        });
    });

    // Handle radio button changes
    agencyRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            agencyCards.forEach(card => {
                card.classList.remove('selected');
                if (card.dataset.agencyId === this.value) {
                    card.classList.add('selected');
                }
            });
            updateAssignButton();
        });
    });

    // Update assign button state
    function updateAssignButton() {
        const selectedAgency = document.querySelector('input[name="agency_id"]:checked');
        assignButton.disabled = !selectedAgency;
    }

    // Handle form submission with confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const selectedAgency = document.querySelector('input[name="agency_id"]:checked');
        if (!selectedAgency) {
            alert('Please select an agency first.');
            return;
        }

        const agencyName = selectedAgency.closest('.agency-card').querySelector('strong').textContent;
        document.getElementById('selectedAgencyName').textContent = agencyName;

        if (hasExistingAssignment) {
            document.getElementById('reassignmentWarning').style.display = 'block';
        }

        confirmModal.show();
    });

    // Initialize selected state if there's an old value
    const checkedRadio = document.querySelector('input[name="agency_id"]:checked');
    if (checkedRadio) {
        const selectedCard = document.querySelector(`[data-agency-id="${checkedRadio.value}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
        updateAssignButton();
    }
});

function submitForm() {
    document.getElementById('assignmentForm').submit();
}
</script>
@endpush
