@extends('layouts.dashboard')

@section('title', 'Assign Inquiries to Agencies - MCMC')

@push('styles')
<style>
.assignment-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
}

.stats-card.success {
    border-left-color: #28a745;
}

.stats-card.warning {
    border-left-color: #ffc107;
}

.stats-card.info {
    border-left-color: #17a2b8;
}

.inquiry-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
    transition: transform 0.2s, box-shadow 0.2s;
}

.inquiry-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.inquiry-card.selected {
    border-left-color: #28a745;
    background: #f8fff8;
}

.bulk-actions {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 2px dashed #dee2e6;
}

.bulk-actions.active {
    border-color: #007bff;
    background: #e7f3ff;
}

.agency-selector {
    position: sticky;
    top: 2rem;
    z-index: 100;
}

.filter-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.recent-assignments {
    max-height: 400px;
    overflow-y: auto;
}

.assignment-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-left: 3px solid #28a745;
}

.assignment-item:last-child {
    margin-bottom: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="assignment-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas fa-tasks me-2"></i>
                    Assign Inquiries to Agencies
                </h1>
                <p class="mb-0 opacity-75">
                    Select inquiries and assign them to appropriate agencies for processing
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="text-white-50">
                    <small>{{ now()->format('l, F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card warning">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-0">{{ $stats['total_unassigned'] }}</h5>
                        <small class="text-muted">Unassigned Inquiries</small>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-0">{{ $stats['total_assigned'] }}</h5>
                        <small class="text-muted">Assigned Inquiries</small>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card info">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-0">{{ $stats['total_completed'] }}</h5>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-flag-checkered fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-0">{{ $stats['total_agencies'] }}</h5>
                        <small class="text-muted">Available Agencies</small>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Unassigned Inquiries -->
        <div class="col-lg-8">
            <!-- Filters -->
            <div class="filter-section">
                <h5 class="mb-3">
                    <i class="fas fa-filter me-2"></i>
                    Filter Inquiries
                </h5>
                <form method="GET" action="{{ route('mcmc.inquiries.assign-page') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($filters['categories'] as $key => $value)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Search inquiries..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">From Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">To Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('mcmc.inquiries.assign-page') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Assignment Section -->
            <div class="bulk-actions" id="bulk-actions" style="display: none;">
                <h5 class="mb-3">
                    <i class="fas fa-tasks me-2"></i>
                    Bulk Assignment
                    <span class="badge bg-primary ms-2" id="selected-count">0</span>
                </h5>

                <form method="POST" action="{{ route('mcmc.inquiries.bulk-assign') }}" id="bulkAssignmentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="bulk_agency_id" class="form-label">Select Agency</label>
                            <select name="agency_id" id="bulk_agency_id" class="form-select" required>
                                <option value="">Choose an agency...</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->agency_ID }}">
                                        {{ $agency->agency_Name }} ({{ $agency->formatted_agency_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bulk_comments" class="form-label">Comments (Optional)</label>
                            <textarea name="comments" id="bulk_comments" class="form-control" rows="2"
                                      placeholder="Add any comments for this assignment..."></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-share-alt"></i> Assign Selected Inquiries
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                            <i class="fas fa-times"></i> Clear Selection
                        </button>
                    </div>
                    <input type="hidden" name="inquiry_ids" id="inquiry_ids" value="">
                </form>
            </div>

            <!-- Unassigned Inquiries List -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Unassigned Inquiries ({{ $unassignedInquiries->total() }})
                        </h5>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($unassignedInquiries->count() > 0)
                        @foreach($unassignedInquiries as $inquiry)
                            <div class="inquiry-card" data-inquiry-id="{{ $inquiry->inquiry_ID }}">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <input type="checkbox" class="form-check-input inquiry-checkbox"
                                               value="{{ $inquiry->inquiry_ID }}"
                                               onchange="updateSelection()">
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            <a href="{{ route('mcmc.inquiries.show', $inquiry->inquiry_ID) }}"
                                               class="text-decoration-none">
                                                {{ $inquiry->inquiry_Title }}
                                            </a>
                                        </h6>
                                        <p class="text-muted mb-1 small">
                                            {{ Str::limit($inquiry->inquiry_Description, 100) }}
                                        </p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-info">{{ $inquiry->formatted_inquiry_type }}</span>
                                            @php
                                                $statusColor = match($inquiry->inquiry_Status ?? 'submitted') {
                                                    'submitted' => 'warning',
                                                    'under_review' => 'info',
                                                    'pending' => 'warning',
                                                    'in_progress' => 'primary',
                                                    'completed' => 'success',
                                                    'rejected' => 'danger',
                                                    'approved' => 'success',
                                                    'closed' => 'dark',
                                                    'assign_to_agency' => 'info',
                                                    default => 'primary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ $inquiry->formatted_status }}</span>
                                            @if($inquiry->hasAttachment())
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-paperclip"></i> Attachment
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> {{ $inquiry->user->name ?? 'Unknown' }}
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ $inquiry->inquiry_Created_At->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('mcmc.inquiries.assign-form', $inquiry->inquiry_ID) }}"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-share-alt"></i> Assign
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $unassignedInquiries->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Unassigned Inquiries</h5>
                            <p class="text-muted">All inquiries have been assigned to agencies.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Agency Selector & Recent Assignments -->
        <div class="col-lg-4">
            <div class="agency-selector">
                <!-- Quick Assignment -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-zap me-2"></i>
                            Quick Assignment
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('mcmc.inquiries.bulk-assign') }}" id="quickAssignForm">
                            @csrf
                            <div class="mb-3">
                                <label for="quick_agency_id" class="form-label">Select Agency</label>
                                <select name="agency_id" id="quick_agency_id" class="form-select" required>
                                    <option value="">Choose an agency...</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->agency_ID }}">
                                            {{ $agency->agency_Name }}
                                            <small>({{ $agency->formatted_agency_type }})</small>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quick_comments" class="form-label">Comments</label>
                                <textarea name="comments" id="quick_comments" class="form-control" rows="2"
                                          placeholder="Assignment comments..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" disabled id="quickAssignBtn">
                                <i class="fas fa-share-alt"></i> Assign Selected
                            </button>
                            <input type="hidden" name="inquiry_ids" id="quick_inquiry_ids" value="">
                        </form>
                    </div>
                </div>

                <!-- Agencies List -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Available Agencies
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="max-height: 300px; overflow-y: auto;">
                            @foreach($agencies as $agency)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div>
                                        <strong>{{ $agency->agency_Name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $agency->formatted_agency_type }}</small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="selectAgency({{ $agency->agency_ID }}, '{{ $agency->agency_Name }}')">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Recent Assignments
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="recent-assignments">
                            @if($assignedInquiries->count() > 0)
                                @foreach($assignedInquiries as $assigned)
                                    <div class="assignment-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($assigned->inquiry_Title, 40) }}</h6>
                                                <small class="text-muted">
                                                    @if($assigned->assignments->first())
                                                        {{ $assigned->assignments->first()->agency->agency_Name }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    {{ $assigned->inquiry_Created_At->format('M d') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-inbox text-muted"></i>
                                    <p class="text-muted mb-0">No recent assignments</p>
                                </div>
                            @endif
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
let selectedInquiries = [];

function updateSelection() {
    const checkboxes = document.querySelectorAll('.inquiry-checkbox:checked');
    selectedInquiries = Array.from(checkboxes).map(cb => cb.value);

    const count = selectedInquiries.length;
    document.getElementById('selected-count').textContent = count;
    document.getElementById('inquiry_ids').value = selectedInquiries.join(',');
    document.getElementById('quick_inquiry_ids').value = selectedInquiries.join(',');

    // Show/hide bulk actions
    const bulkActions = document.getElementById('bulk-actions');
    const quickAssignBtn = document.getElementById('quickAssignBtn');

    if (count > 0) {
        bulkActions.style.display = 'block';
        bulkActions.classList.add('active');
        quickAssignBtn.disabled = false;
    } else {
        bulkActions.style.display = 'none';
        bulkActions.classList.remove('active');
        quickAssignBtn.disabled = true;
    }

    // Update inquiry card styling
    document.querySelectorAll('.inquiry-card').forEach(card => {
        const inquiryId = card.getAttribute('data-inquiry-id');
        if (selectedInquiries.includes(inquiryId)) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });
}

function selectAll() {
    document.querySelectorAll('.inquiry-checkbox').forEach(cb => {
        cb.checked = true;
    });
    updateSelection();
}

function deselectAll() {
    document.querySelectorAll('.inquiry-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateSelection();
}

function clearSelection() {
    deselectAll();
}

function selectAgency(agencyId, agencyName) {
    document.getElementById('bulk_agency_id').value = agencyId;
    document.getElementById('quick_agency_id').value = agencyId;

    // Show notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-info alert-dismissible fade show position-fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        Agency "${agencyName}" selected for assignment
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Form validation
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
        alert('Please select an agency to assign the inquiries to.');
        return;
    }

    // Confirm bulk assignment
    const count = selectedInquiries.length;
    const agencyName = agencySelect.options[agencySelect.selectedIndex].text;

    if (!confirm(`Are you sure you want to assign ${count} inquiries to ${agencyName}?`)) {
        e.preventDefault();
        return;
    }
});

document.getElementById('quickAssignForm').addEventListener('submit', function(e) {
    const selectedInquiries = document.querySelectorAll('.inquiry-checkbox:checked');
    const agencySelect = document.getElementById('quick_agency_id');

    if (selectedInquiries.length === 0) {
        e.preventDefault();
        alert('Please select at least one inquiry to assign.');
        return;
    }

    if (!agencySelect.value) {
        e.preventDefault();
        alert('Please select an agency to assign the inquiries to.');
        return;
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSelection();
});
</script>
@endpush
