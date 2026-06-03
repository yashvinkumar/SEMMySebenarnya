@extends('layouts.dashboard')

@section('title', 'Inquiry Details - MCMC')

@push('styles')
<style>
.inquiry-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.detail-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -0.5rem;
    top: 0.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #007bff;
}

.timeline-item.completed::before {
    background: #28a745;
    box-shadow: 0 0 0 3px #28a745;
}

.timeline-item.warning::before {
    background: #ffc107;
    box-shadow: 0 0 0 3px #ffc107;
}

.timeline-item.danger::before {
    background: #dc3545;
    box-shadow: 0 0 0 3px #dc3545;
}

.status-timeline {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.assignment-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.user-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.action-buttons {
    position: sticky;
    top: 2rem;
    z-index: 100;
}

.attachment-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
}

.priority-high {
    border-left-color: #dc3545 !important;
}

.priority-medium {
    border-left-color: #ffc107 !important;
}

.priority-low {
    border-left-color: #28a745 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('mcmc.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('mcmc.inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item active">Inquiry #{{ $inquiry->inquiry_ID }}</li>
                </ol>
            </nav>

            <!-- Inquiry Header -->
            <div class="inquiry-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-2">{{ $inquiry->inquiry_Title }}</h1>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @php
                                $statusColor = match($inquiry->inquiry_Status ?? 'submitted') {
                                    'submitted' => 'warning',
                                    'under_review' => 'info',
                                    'assigned_to_agency' => 'primary',
                                    'agency_review_in_progress' => 'primary',
                                    'agency_review_completed' => 'success',
                                    'agency_rejected' => 'danger',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'closed' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="status-badge bg-{{ $statusColor }}">
                                {{ $inquiry->formatted_status }}
                            </span>
                            <span class="badge bg-light text-dark">{{ $inquiry->formatted_inquiry_type }}</span>
                            <span class="badge bg-light text-dark">ID: #{{ $inquiry->inquiry_ID }}</span>
                        </div>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Submitted {{ $inquiry->inquiry_Created_At->format('M d, Y \a\t H:i') }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        @if($inquiry->hasAttachment())
                            <div class="mb-2">
                                <i class="fas fa-paperclip me-1"></i>
                                <small>Has Attachment</small>
                            </div>
                        @endif
                        <div class="text-white-50">
                            Last updated {{ $inquiry->inquiry_Created_At->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Inquiry Details -->
            <div class="detail-card">
                <h4 class="mb-3">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Inquiry Details
                </h4>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Category:</strong>
                        <span class="ms-2">{{ $inquiry->formatted_inquiry_type }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Priority:</strong>
                        <span class="ms-2 badge bg-warning">Medium</span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Description:</strong>
                    <div class="bg-light p-3 rounded mt-2">
                        {{ $inquiry->inquiry_Description }}
                    </div>
                </div>

                @if($inquiry->hasAttachment())
                    <div class="mb-3">
                        <strong>Attachment:</strong>
                        <div class="attachment-preview mt-2">
                            <i class="fas fa-file fa-2x text-muted mb-2"></i>
                            <div>
                                <strong>{{ $inquiry->attachment_filename }}</strong>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Current Assignment -->
            @if($inquiry->currentAssignment())
                <div class="detail-card">
                    <h4 class="mb-3">
                        <i class="fas fa-building text-primary me-2"></i>
                        Current Assignment
                    </h4>

                    @php $assignment = $inquiry->currentAssignment() @endphp
                    <div class="assignment-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ $assignment->agency->agency_Name }}</h5>
                                <p class="text-muted mb-2">{{ $assignment->agency->formatted_agency_type }}</p>
                                <p class="mb-1">
                                    <i class="fas fa-envelope me-2"></i>{{ $assignment->agency->agency_Email }}
                                </p>
                                @if($assignment->agency->agency_Phone)
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2"></i>{{ $assignment->agency->agency_Phone }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Status:</strong>
                                    @php
                                        $assignmentStatusColor = match($assignment->assignment_Status ?? 'pending') {
                                            'pending' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'rejected' => 'danger',
                                            'reassigned' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="ms-2 badge bg-{{ $assignmentStatusColor }}">
                                        {{ ucfirst($assignment->assignment_Status) }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <strong>Assigned:</strong>
                                    <span class="ms-2">{{ $assignment->assignment_Date->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Assigned by:</strong>
                                    <span class="ms-2">{{ $assignment->assignedByStaff->staff_Name ?? 'System' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($assignment->assignment_Comments)
                            <div class="mt-3">
                                <strong>Assignment Comments:</strong>
                                <div class="bg-white p-2 rounded mt-1">
                                    {{ $assignment->assignment_Comments }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Assignment History -->
            @if($assignmentHistory->count() > 0)
                <div class="detail-card">
                    <h4 class="mb-3">
                        <i class="fas fa-history text-primary me-2"></i>
                        Assignment History
                    </h4>

                    <div class="timeline">
                        @foreach($assignmentHistory as $assignment)
                            @php
                                $timelineClass = match($assignment->assignment_Status ?? 'pending') {
                                    'completed' => 'completed',
                                    'rejected' => 'danger',
                                    'reassigned' => 'warning',
                                    default => ''
                                };
                            @endphp
                            <div class="timeline-item {{ $timelineClass }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $assignment->agency->agency_Name }}</h6>
                                        <p class="text-muted mb-1">{{ $assignment->agency->formatted_agency_type }}</p>
                                        @php
                                            $timelineAssignmentStatusColor = match($assignment->assignment_Status ?? 'pending') {
                                                'pending' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'rejected' => 'danger',
                                                'reassigned' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $timelineAssignmentStatusColor }}">
                                            {{ ucfirst($assignment->assignment_Status) }}
                                        </span>
                                        @if($assignment->assignment_Comments)
                                            <div class="mt-2">
                                                <small class="text-muted">{{ $assignment->assignment_Comments }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            {{ $assignment->assignment_Date->format('M d, Y') }}<br>
                                            {{ $assignment->assignment_Date->format('H:i') }}
                                        </small>
                                        @if($assignment->assignedByStaff)
                                            <div class="mt-1">
                                                <small class="text-muted">by {{ $assignment->assignedByStaff->staff_Name }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Status Update Form -->
            <div class="detail-card">
                <h4 class="mb-3">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Update Status
                </h4>

                <form method="POST" action="{{ route('mcmc.inquiries.update-status', $inquiry->inquiry_ID) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="status" class="form-label">New Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="pending" {{ $inquiry->inquiry_Status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="under_review" {{ $inquiry->inquiry_Status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="approved" {{ $inquiry->inquiry_Status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $inquiry->inquiry_Status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="closed" {{ $inquiry->inquiry_Status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea name="comments" id="comments" class="form-control" rows="3"
                                  placeholder="Add comments about the status change..."></textarea>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="action-buttons">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Actions</h5>
                        <div class="d-grid gap-2">
                            @if($inquiry->canBeAssigned())
                                <a href="{{ route('mcmc.inquiries.assign-form', $inquiry->inquiry_ID) }}"
                                   class="btn btn-success">
                                    <i class="fas fa-share-alt"></i>
                                    {{ $inquiry->currentAssignment() ? 'Reassign to Agency' : 'Assign to Agency' }}
                                </a>
                            @endif

                            <button class="btn btn-outline-primary" onclick="exportInquiry()">
                                <i class="fas fa-download"></i> Export Details
                            </button>

                            <button class="btn btn-outline-info" onclick="printInquiry()">
                                <i class="fas fa-print"></i> Print
                            </button>

                            <a href="{{ route('mcmc.inquiries.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Submitted By</h5>
                        <div class="user-info">
                            <div class="text-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    {{ strtoupper(substr($inquiry->user->name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <div class="text-center">
                                <h6 class="mb-1">{{ $inquiry->user->name ?? 'Unknown User' }}</h6>
                                <p class="text-muted mb-2">{{ $inquiry->user->email ?? 'No email' }}</p>
                                @if($inquiry->user->phone ?? false)
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-phone me-1"></i>{{ $inquiry->user->phone }}
                                    </p>
                                @endif
                                <div class="d-flex justify-content-center gap-2">
                                    @if($inquiry->user->email ?? false)
                                        <a href="mailto:{{ $inquiry->user->email }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-sm btn-outline-info" onclick="viewUserProfile()">
                                        <i class="fas fa-user"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Stats</h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="h4 mb-0 text-primary">{{ $assignmentHistory->count() }}</div>
                                    <small class="text-muted">Assignments</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="h4 mb-0 text-info">{{ $inquiry->inquiry_Created_At->diffInDays() }}</div>
                                    <small class="text-muted">Days Old</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportInquiry() {
    window.open(`/mcmc/inquiries/{{ $inquiry->inquiry_ID }}/export`, '_blank');
}

function printInquiry() {
    window.print();
}

function viewUserProfile() {
    // Implement user profile modal or redirect
    alert('User profile feature coming soon!');
}

// Helper functions moved to blade templates above
</script>
@endpush
