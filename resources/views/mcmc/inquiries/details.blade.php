@extends('layouts.dashboard')

@section('title', 'Inquiry Details - #' . $inquiry->inquiry_ID)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Inquiry Details Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Inquiry #{{ $inquiry->inquiry_ID }}</h4>
                    <div>
                        @php
                            $statusClass = match($inquiry->inquiry_Status ?? 'submitted') {
                                'submitted' => 'badge-warning',
                                'under_review' => 'badge-info',
                                'assigned_to_agency' => 'badge-primary',
                                'agency_review_in_progress' => 'badge-primary',
                                'agency_review_completed' => 'badge-success',
                                'agency_rejected' => 'badge-danger',
                                'approved' => 'badge-success',
                                'rejected' => 'badge-danger',
                                'closed' => 'badge-secondary',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $inquiry->formatted_status }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Inquiry Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Title:</strong></td>
                                    <td>{{ $inquiry->inquiry_Title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><span class="badge badge-info">{{ ucfirst($inquiry->inquiry_Category ?? 'N/A') }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Submitted:</strong></td>
                                    <td>{{ $inquiry->inquiry_Created_At ? $inquiry->inquiry_Created_At->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge {{ $statusClass }}">{{ $inquiry->formatted_status }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>User Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Name:</strong></td>
                                    <td>{{ $inquiry->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $inquiry->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $inquiry->user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td>#{{ $inquiry->user_ID }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Description</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($inquiry->inquiry_Description)) !!}
                        </div>
                    </div>

                    @if($inquiry->inquiry_Attachment_URL)
                        <div class="mb-4">
                            <h6>Attachments</h6>
                            <div class="border rounded p-3">
                                <a href="{{ $inquiry->inquiry_Attachment_URL }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download"></i> Download Attachment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assignment History -->
            @if($inquiry->assignments && $inquiry->assignments->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Assignment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($inquiry->assignments->sortByDesc('assignment_Date') as $assignment)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $assignment->assignment_Status === 'completed' ? 'success' : ($assignment->assignment_Status === 'rejected' ? 'danger' : 'primary') }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            Assigned to {{ $assignment->agency->agency_Name }}
                                            @php
                                                $assignmentStatusClass = match($assignment->assignment_Status) {
                                                    'pending' => 'badge-warning',
                                                    'in_progress' => 'badge-info',
                                                    'completed' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    'reassigned' => 'badge-secondary',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $assignmentStatusClass }} ms-2">
                                                {{ ucfirst($assignment->assignment_Status) }}
                                            </span>
                                        </h6>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-calendar"></i> {{ $assignment->assignment_Date->format('M d, Y H:i') }}
                                            @if($assignment->assignedByStaff)
                                                | <i class="fas fa-user"></i> by {{ $assignment->assignedByStaff->staff_Name }}
                                            @endif
                                        </p>

                                        @if($assignment->assignment_Comments)
                                            <div class="mt-2">
                                                <strong>Comments:</strong>
                                                <div class="text-muted">{!! nl2br(e($assignment->assignment_Comments)) !!}</div>
                                            </div>
                                        @endif

                                        @if($assignment->rejection_Reason)
                                            <div class="mt-2">
                                                <strong>Rejection Reason:</strong>
                                                <div class="text-danger">{!! nl2br(e($assignment->rejection_Reason)) !!}</div>
                                            </div>
                                        @endif

                                        @if($assignment->completed_At)
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Completed on {{ $assignment->completed_At->format('M d, Y H:i') }}
                                                    ({{ $assignment->assignment_Date->diffInHours($assignment->completed_At) }} hours)
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($inquiry->inquiry_Status ?? 'submitted', ['submitted', 'under_review']))
                            <a href="{{ route('mcmc.assignments.assign-form', $inquiry->inquiry_ID) }}"
                               class="btn btn-success">
                                <i class="fas fa-share"></i> Assign to Agency
                            </a>
                        @endif

                        @php
                            $latestAssignment = $inquiry->assignments ? $inquiry->assignments->first() : null;
                        @endphp
                        @if($latestAssignment && $latestAssignment->assignment_Status === 'rejected')
                            <button type="button" class="btn btn-warning"
                                    onclick="showReassignModal({{ $inquiry->inquiry_ID }}, {{ $latestAssignment->assignment_ID }})">
                                <i class="fas fa-redo"></i> Reassign to Different Agency
                            </button>
                        @endif

                        <button type="button" class="btn btn-info" onclick="showStatusUpdateModal()">
                            <i class="fas fa-edit"></i> Update Status
                        </button>

                        <a href="{{ route('mcmc.inquiries.list') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Current Assignment Info -->
            @if($latestAssignment && in_array($latestAssignment->assignment_Status, ['pending', 'in_progress']))
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Current Assignment</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Agency:</strong></td>
                                <td>{{ $latestAssignment->agency->agency_Name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Assigned:</strong></td>
                                <td>{{ $latestAssignment->assignment_Date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @php
                                        $assignmentStatusClass = match($latestAssignment->assignment_Status) {
                                            'pending' => 'badge-warning',
                                            'in_progress' => 'badge-info',
                                            'completed' => 'badge-success',
                                            'rejected' => 'badge-danger',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $assignmentStatusClass }}">
                                        {{ ucfirst($latestAssignment->assignment_Status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Days Elapsed:</strong></td>
                                <td>{{ $latestAssignment->assignment_Date->diffInDays(now()) }} days</td>
                            </tr>
                        </table>

                        @if($latestAssignment->assignment_Status === 'pending' &&
                            $latestAssignment->assignment_Date->diffInHours(now()) > 48)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Overdue:</strong> This assignment has been pending for more than 48 hours.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Agency Contact (if assigned) -->
            @if($latestAssignment && in_array($latestAssignment->assignment_Status, ['pending', 'in_progress']))
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Agency Contact</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $latestAssignment->agency->agency_Name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ $latestAssignment->agency->agency_Type ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>
                                    <a href="mailto:{{ $latestAssignment->agency->agency_Email }}">
                                        {{ $latestAssignment->agency->agency_Email }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $latestAssignment->agency->agency_Phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Inquiry Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('mcmc.inquiries.update-status', $inquiry->inquiry_ID) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="submitted" {{ $inquiry->inquiry_Status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="under_review" {{ $inquiry->inquiry_Status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="assigned_to_agency" {{ $inquiry->inquiry_Status === 'assigned_to_agency' ? 'selected' : '' }}>Assigned to Agency</option>
                            <option value="agency_review_in_progress" {{ $inquiry->inquiry_Status === 'agency_review_in_progress' ? 'selected' : '' }}>Agency Review in Progress</option>
                            <option value="agency_review_completed" {{ $inquiry->inquiry_Status === 'agency_review_completed' ? 'selected' : '' }}>Agency Review Completed</option>
                            <option value="agency_rejected" {{ $inquiry->inquiry_Status === 'agency_rejected' ? 'selected' : '' }}>Agency Rejected</option>
                            <option value="approved" {{ $inquiry->inquiry_Status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $inquiry->inquiry_Status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="closed" {{ $inquiry->inquiry_Status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea name="comments" id="comments" class="form-control" rows="3"
                                placeholder="Optional comments about the status change..."></textarea>
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

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reassign Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reassignForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_agency_id" class="form-label">Select New Agency</label>
                        <select name="new_agency_id" id="new_agency_id" class="form-control" required>
                            <option value="">Choose Agency</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->agency_ID }}">{{ $agency->agency_Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reassignment_reason" class="form-label">Reassignment Reason</label>
                        <textarea name="reassignment_reason" id="reassignment_reason"
                                class="form-control" rows="3" required
                                placeholder="Explain why this inquiry is being reassigned..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reassign Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -24px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
function showStatusUpdateModal() {
    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    modal.show();
}

function showReassignModal(inquiryId, assignmentId) {
    const form = document.getElementById('reassignForm');
    form.action = `/mcmc/assignments/${assignmentId}/reassign`;

    const modal = new bootstrap.Modal(document.getElementById('reassignModal'));
    modal.show();
}
</script>
@endpush
