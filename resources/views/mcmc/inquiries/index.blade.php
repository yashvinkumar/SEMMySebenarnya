@extends('layouts.dashboard')

@section('title', 'MCMC - Inquiries Management')

@push('styles')
<style>
.inquiry-card {
    transition: transform 0.2s ease-in-out;
}
.inquiry-card:hover {
    transform: translateY(-2px);
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.assignment-info {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 0.5rem;
    margin-top: 0.5rem;
}
.filter-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.bulk-actions {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Inquiries Management</h1>
                    <p class="text-muted">Manage and assign public inquiries to relevant agencies</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="toggleBulkActions()" id="bulkToggleBtn">
                        <i class="fas fa-tasks"></i> Bulk Actions
                    </button>
                    <a href="{{ route('mcmc.assignments.reports') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <button class="btn btn-secondary" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
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

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('bulk_errors'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Some errors occurred during bulk assignment:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach(session('bulk_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('mcmc.inquiries.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($filters['statuses'] as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($filters['categories'] as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="assignment_filter" class="form-label">Assignment Status</label>
                            <select name="assignment_filter" id="assignment_filter" class="form-select">
                                @foreach($filters['assignment_statuses'] as $key => $label)
                                    <option value="{{ $key }}" {{ request('assignment_filter') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="agency_id" class="form-label">Agency</label>
                            <select name="agency_id" id="agency_id" class="form-select">
                                <option value="">All Agencies</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->agency_ID }}" {{ request('agency_id') == $agency->agency_ID ? 'selected' : '' }}>
                                        {{ $agency->agency_Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Search by title, description, or user..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('mcmc.inquiries.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                            <div class="float-end">
                                <small class="text-muted">Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }} results</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Actions Form -->
            <div id="bulk-actions" class="bulk-actions" style="display: none;">
                <form method="POST" action="{{ route('mcmc.inquiries.bulk-assign') }}" id="bulkAssignmentForm">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="bulk_agency_id" class="form-label">Select Agency</label>
                            <select name="agency_id" id="bulk_agency_id" class="form-select" required>
                                <option value="">Choose Agency</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->agency_ID }}">
                                        {{ $agency->agency_Name }} ({{ $agency->formatted_agency_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="bulk_comments" class="form-label">Comments</label>
                            <textarea name="comments" id="bulk_comments" class="form-control" rows="1"
                                    placeholder="Assignment comments (optional)"></textarea>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success me-2" disabled id="bulkAssignBtn">
                                <i class="fas fa-share"></i> Assign Selected (<span id="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Inquiries Table -->
            <div class="card">
                <div class="card-body p-0">
                    @if($inquiries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40px">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th width="80px">ID</th>
                                        <th>Title & Description</th>
                                        <th>User</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Assignment</th>
                                        <th>Date</th>
                                        <th width="120px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inquiries as $inquiry)
                                        <tr class="inquiry-row">
                                            <td>
                                                <input type="checkbox" name="inquiry_ids[]" value="{{ $inquiry->inquiry_ID }}"
                                                       class="form-check-input inquiry-checkbox">
                                            </td>
                                            <td>
                                                <strong class="text-primary">#{{ $inquiry->inquiry_ID }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ Str::limit($inquiry->inquiry_Title, 50) }}</strong>
                                                    @if($inquiry->hasAttachment())
                                                        <i class="fas fa-paperclip text-muted ms-1" title="Has attachment"></i>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ Str::limit($inquiry->inquiry_Description, 80) }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $inquiry->user->name ?? 'N/A' }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $inquiry->user->email ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $inquiry->formatted_inquiry_type }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($inquiry->inquiry_Status ?? 'submitted') {
                                                        'submitted' => 'bg-warning',
                                                        'under_review' => 'bg-info',
                                                        'assigned_to_agency' => 'bg-primary',
                                                        'agency_review_in_progress' => 'bg-primary',
                                                        'agency_review_completed' => 'bg-success',
                                                        'agency_rejected' => 'bg-danger',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        'closed' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge status-badge {{ $statusClass }}">
                                                    {{ $inquiry->formatted_status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($inquiry->latestAssignment)
                                                    <div class="assignment-info">
                                                        <strong>{{ $inquiry->latestAssignment->agency->agency_Name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $inquiry->latestAssignment->assignment_Date->format('M d, Y') }}
                                                        </small>
                                                        <br>
                                                        @php
                                                            $assignmentStatusColor = match($inquiry->latestAssignment->assignment_Status ?? 'pending') {
                                                                'pending' => 'warning',
                                                                'in_progress' => 'info',
                                                                'completed' => 'success',
                                                                'rejected' => 'danger',
                                                                'reassigned' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $assignmentStatusColor }}">
                                                            {{ ucfirst($inquiry->latestAssignment->assignment_Status) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-times-circle"></i> Not assigned
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $inquiry->inquiry_Created_At->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $inquiry->inquiry_Created_At->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('mcmc.inquiries.show', $inquiry->inquiry_ID) }}"
                                                       class="btn btn-outline-primary btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if($inquiry->canBeAssigned())
                                                        <a href="{{ route('mcmc.inquiries.assign-form', $inquiry->inquiry_ID) }}"
                                                           class="btn btn-outline-success btn-sm" title="Assign to Agency">
                                                            <i class="fas fa-share-alt"></i>
                                                        </a>
                                                    @endif

                                                    @if($inquiry->latestAssignment)
                                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                                                onclick="showReassignModal({{ $inquiry->inquiry_ID }})" title="Reassign">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $inquiries->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4>No inquiries found</h4>
                            <p class="text-muted">Try adjusting your search criteria or filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reassignment Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reassign Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="reassignForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reassign_agency_id" class="form-label">New Agency</label>
                        <select name="agency_id" id="reassign_agency_id" class="form-select" required>
                            <option value="">Select Agency</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->agency_ID }}">{{ $agency->agency_Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reassignment_reason" class="form-label">Reason for Reassignment</label>
                        <textarea name="reassignment_reason" id="reassignment_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="reassign_comments" class="form-label">Additional Comments</label>
                        <textarea name="comments" id="reassign_comments" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reassign Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const inquiryCheckboxes = document.querySelectorAll('.inquiry-checkbox');
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    selectAllCheckbox.addEventListener('change', function() {
        inquiryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkAssignButton();
    });

    inquiryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateBulkAssignButton();
        });
    });

    function updateSelectAllCheckbox() {
        const checkedCount = document.querySelectorAll('.inquiry-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === inquiryCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < inquiryCheckboxes.length;
    }

    function updateBulkAssignButton() {
        const checkedCount = document.querySelectorAll('.inquiry-checkbox:checked').length;
        bulkAssignBtn.disabled = checkedCount === 0;
        selectedCountSpan.textContent = checkedCount;
    }

    // Bulk assignment form submission
    document.getElementById('bulkAssignmentForm').addEventListener('submit', function(e) {
        const selectedInquiries = document.querySelectorAll('.inquiry-checkbox:checked');
        const agencySelect = document.getElementById('bulk_agency_id');

        if (selectedInquiries.length === 0) {
            e.preventDefault();
            alert('Please select at least one inquiry to assign.');
            return;
        }

        if (!agencySelect.value) {
            e.preventDefault();
            alert('Please select an agency.');
            return;
        }

        const agencyName = agencySelect.options[agencySelect.selectedIndex].text.split(' (')[0];
        if (!confirm(`Are you sure you want to assign ${selectedInquiries.length} inquiries to ${agencyName}?`)) {
            e.preventDefault();
        }
    });

    // Auto-submit form on filter change
    document.querySelectorAll('#status, #category, #assignment_filter, #agency_id').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const toggleBtn = document.getElementById('bulkToggleBtn');

    if (bulkActions.style.display === 'none') {
        bulkActions.style.display = 'block';
        toggleBtn.classList.add('active');
        toggleBtn.innerHTML = '<i class="fas fa-tasks"></i> Hide Bulk Actions';
    } else {
        bulkActions.style.display = 'none';
        toggleBtn.classList.remove('active');
        toggleBtn.innerHTML = '<i class="fas fa-tasks"></i> Bulk Actions';
        clearSelection();
    }
}

function clearSelection() {
    document.querySelectorAll('.inquiry-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('bulkAssignBtn').disabled = true;
    document.getElementById('selectedCount').textContent = '0';
}

function showReassignModal(inquiryId) {
    const modal = new bootstrap.Modal(document.getElementById('reassignModal'));
    const form = document.getElementById('reassignForm');
    form.action = `/mcmc/inquiries/${inquiryId}/reassign`;
    modal.show();
}

function exportData() {
    const url = new URL(window.location.href);
    url.pathname = '/mcmc/inquiries/export';
    window.open(url.toString(), '_blank');
}

// Helper functions for status colors (moved to blade templates above)
</script>
@endpush
