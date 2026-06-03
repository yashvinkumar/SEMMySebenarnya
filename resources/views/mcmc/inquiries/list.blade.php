@extends('layouts.dashboard')

@section('title', 'MCMC - Inquiries Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Inquiries Management</h4>
                    <div>
                        <button class="btn btn-primary" onclick="toggleBulkActions()">
                            <i class="fas fa-tasks"></i> Bulk Actions
                        </button>
                        <a href="{{ route('mcmc.assignments.reports') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body">
                    <form method="GET" action="{{ route('mcmc.inquiries.list') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="assigned_to_agency" {{ request('status') == 'assigned_to_agency' ? 'selected' : '' }}>Assigned to Agency</option>
                                    <option value="agency_review_in_progress" {{ request('status') == 'agency_review_in_progress' ? 'selected' : '' }}>Agency Review in Progress</option>
                                    <option value="agency_review_completed" {{ request('status') == 'agency_review_completed' ? 'selected' : '' }}>Agency Review Completed</option>
                                    <option value="agency_rejected" {{ request('status') == 'agency_rejected' ? 'selected' : '' }}>Agency Rejected</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <option value="broadcasting" {{ request('category') == 'broadcasting' ? 'selected' : '' }}>Broadcasting</option>
                                    <option value="telecommunications" {{ request('category') == 'telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                                    <option value="internet" {{ request('category') == 'internet' ? 'selected' : '' }}>Internet Services</option>
                                    <option value="multimedia" {{ request('category') == 'multimedia' ? 'selected' : '' }}>Multimedia Content</option>
                                    <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical Issues</option>
                                    <option value="complaint" {{ request('category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="assignment_filter">Assignment Status</label>
                                <select name="assignment_filter" id="assignment_filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="assigned" {{ request('assignment_filter') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="unassigned" {{ request('assignment_filter') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Search by title or description" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('mcmc.inquiries.list') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions Form (Hidden by default) -->
                    <div id="bulk-actions-form" style="display: none;" class="mb-4">
                        <form method="POST" action="{{ route('mcmc.assignments.bulk-assign') }}">
                            @csrf
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Bulk Assignment</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="agency_id" class="form-control" required>
                                                <option value="">Select Agency</option>
                                                @if(isset($agencies))
                                                    @foreach($agencies as $agency)
                                                        <option value="{{ $agency->agency_ID }}">{{ $agency->agency_Name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea name="comments" class="form-control"
                                                    placeholder="Assignment comments (optional)" rows="1"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-share"></i> Assign Selected
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Inquiries Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th width="5%">ID</th>
                                    <th width="20%">Title</th>
                                    <th width="12%">User</th>
                                    <th width="10%">Category</th>
                                    <th width="12%">Status</th>
                                    <th width="15%">Assignment</th>
                                    <th width="10%">Date</th>
                                    <th width="13%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($inquiries) && $inquiries->count() > 0)
                                    @foreach($inquiries as $inquiry)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="inquiry_ids[]" value="{{ $inquiry->inquiry_ID }}"
                                                       class="form-check-input inquiry-checkbox">
                                            </td>
                                            <td>
                                                <strong>#{{ $inquiry->inquiry_ID }}</strong>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $inquiry->inquiry_Title }}">
                                                    {{ $inquiry->inquiry_Title }}
                                                </div>
                                                <small class="text-muted">{{ Str::limit($inquiry->inquiry_Description, 50) }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $inquiry->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $inquiry->user->email ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($inquiry->inquiry_Category ?? 'N/A') }}</span>
                                            </td>
                                            <td>
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
                                                <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status ?? 'submitted')) }}</span>
                                            </td>
                                            <td>
                                                @if(isset($inquiry->latestAssignment) && $inquiry->latestAssignment)
                                                    <div>
                                                        <strong>{{ $inquiry->latestAssignment->agency->agency_Name ?? 'N/A' }}</strong>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $inquiry->latestAssignment->assignment_Date ? $inquiry->latestAssignment->assignment_Date->format('M d, Y') : 'N/A' }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $inquiry->inquiry_Created_At ? $inquiry->inquiry_Created_At->format('M d, Y') : 'N/A' }}</div>
                                                <small class="text-muted">{{ $inquiry->inquiry_Created_At ? $inquiry->inquiry_Created_At->format('H:i') : '' }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('mcmc.inquiries.details', $inquiry->inquiry_ID) }}"
                                                       class="btn btn-outline-primary btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(in_array($inquiry->inquiry_Status ?? 'submitted', ['submitted', 'under_review']))
                                                        <a href="{{ route('mcmc.assignments.assign-form', $inquiry->inquiry_ID) }}"
                                                           class="btn btn-outline-success btn-sm" title="Assign to Agency">
                                                            <i class="fas fa-share"></i>
                                                        </a>
                                                    @endif
                                                    @if(isset($inquiry->latestAssignment) && $inquiry->latestAssignment && $inquiry->latestAssignment->assignment_Status === 'rejected')
                                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                                                onclick="showReassignModal({{ $inquiry->inquiry_ID }}, {{ $inquiry->latestAssignment->assignment_ID }})"
                                                                title="Reassign">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No inquiries found matching your criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($inquiries))
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }}
                                of {{ $inquiries->total() }} results
                            </div>
                            {{ $inquiries->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
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
                            @if(isset($agencies))
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->agency_ID }}">{{ $agency->agency_Name }}</option>
                                @endforeach
                            @endif
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

@push('scripts')
<script>
function toggleBulkActions() {
    const bulkForm = document.getElementById('bulk-actions-form');
    bulkForm.style.display = bulkForm.style.display === 'none' ? 'block' : 'none';
}

// Select all checkbox functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.inquiry-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Show reassign modal
function showReassignModal(inquiryId, assignmentId) {
    const form = document.getElementById('reassignForm');
    form.action = `/mcmc/assignments/${assignmentId}/reassign`;

    const modal = new bootstrap.Modal(document.getElementById('reassignModal'));
    modal.show();
}

// Update bulk actions form with selected inquiries
document.addEventListener('DOMContentLoaded', function() {
    const bulkForm = document.querySelector('#bulk-actions-form form');

    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedInquiries = document.querySelectorAll('.inquiry-checkbox:checked');
            if (selectedInquiries.length === 0) {
                e.preventDefault();
                alert('Please select at least one inquiry to assign.');
                return;
            }

            // Add selected inquiry IDs to form
            selectedInquiries.forEach((checkbox, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'inquiry_ids[]';
                input.value = checkbox.value;
                bulkForm.appendChild(input);
            });
        });
    }
});
</script>
@endpush
