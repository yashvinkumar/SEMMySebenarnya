@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .assignment-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .assignment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .assignment-pending {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fefbf2 0%, #fef3c7 100%);
        }
        .assignment-in-progress {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, #f0f9ff 0%, #dbeafe 100%);
        }
        .assignment-completed {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .assignment-rejected {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }
        .priority-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .priority-urgent {
            background-color: #ef4444;
            animation: pulse 2s infinite;
        }
        .priority-high {
            background-color: #f59e0b;
        }
        .priority-normal {
            background-color: #10b981;
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-chip.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .filter-chip:hover:not(.active) {
            background-color: #e5e7eb;
        }
        .jurisdiction-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .jurisdiction-review-required {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { box-shadow: 0 0 5px rgba(245, 158, 11, 0.5); }
            to { box-shadow: 0 0 15px rgba(245, 158, 11, 0.8); }
        }
        .jurisdiction-accepted {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #10b981;
        }
        .jurisdiction-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        .bulk-actions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .search-box {
            position: relative;
        }
        .search-box .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .search-input {
            padding-left: 3rem;
        }
        .assignment-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .action-btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        .action-btn-primary:hover {
            background-color: #2563eb;
            color: white;
        }
        .action-btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .action-btn-danger:hover {
            background-color: #dc2626;
            color: white;
        }
        .action-btn-success {
            background-color: #10b981;
            color: white;
        }
        .action-btn-success:hover {
            background-color: #059669;
            color: white;
        }
        .action-btn-outline {
            background-color: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        .action-btn-outline:hover {
            background-color: #f9fafb;
            color: #374151;
        }
    </style>
@endpush

@section('title')
    Assignment Management - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('agency.assignments.list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        Assignment Management
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-bell mr-2" aria-hidden="true"></i>
        Notifications
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('agency.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('agency.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
<div class="py-8" style="color:#000 !important;">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assignment Management</h1>
                <p class="text-gray-600 mt-2">Review, accept, and manage your assigned inquiries</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Export Options -->
                <div class="relative">
                    <button onclick="toggleExportMenu()" class="btn btn-outline-secondary inline-flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    <div id="exportMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border hidden z-50">
                        <div class="py-2">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-file-excel mr-2"></i>Export as Excel
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Refresh -->
                <button onclick="refreshAssignments()" class="btn btn-outline-secondary">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-xl font-semibold">{{ $assignments->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-gavel text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Pending Review</p>
                        <p class="text-xl font-semibold text-yellow-600">{{ $assignments->where('assignment_Status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">In Progress</p>
                        <p class="text-xl font-semibold text-blue-600">{{ $assignments->where('assignment_Status', 'in_progress')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-xl font-semibold text-green-600">{{ $assignments->where('assignment_Status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Rejected</p>
                        <p class="text-xl font-semibold text-red-600">{{ $assignments->where('assignment_Status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions (only show if there are pending assignments) -->
        @if($assignments->where('assignment_Status', 'pending')->count() > 0)
        <div class="bulk-actions" id="bulkActions" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="font-semibold">Bulk Actions:</span>
                    <span id="selectedCount">0 assignments selected</span>
                </div>
                <div class="flex space-x-3">
                    <button onclick="bulkAccept()" class="action-btn action-btn-success">
                        <i class="fas fa-check mr-2"></i>Accept Selected
                    </button>
                    <button onclick="bulkReject()" class="action-btn action-btn-danger">
                        <i class="fas fa-times mr-2"></i>Reject Selected
                    </button>
                    <button onclick="clearSelection()" class="action-btn action-btn-outline">
                        <i class="fas fa-times mr-2"></i>Clear Selection
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Search and Filters -->
                <div class="space-y-4">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search assignments..."
                               class="search-input form-input w-full"
                               onkeyup="filterAssignments()">
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="text-sm font-semibold text-gray-700 mr-2">Status:</span>
                        <div class="filter-chip active" data-filter="all" onclick="setStatusFilter('all')">
                            <i class="fas fa-list mr-2"></i>All
                        </div>
                        <div class="filter-chip" data-filter="pending" onclick="setStatusFilter('pending')">
                            <i class="fas fa-gavel mr-2"></i>Pending Review
                        </div>
                        <div class="filter-chip" data-filter="in_progress" onclick="setStatusFilter('in_progress')">
                            <i class="fas fa-spinner mr-2"></i>In Progress
                        </div>
                        <div class="filter-chip" data-filter="completed" onclick="setStatusFilter('completed')">
                            <i class="fas fa-check-circle mr-2"></i>Completed
                        </div>
                        <div class="filter-chip" data-filter="rejected" onclick="setStatusFilter('rejected')">
                            <i class="fas fa-times-circle mr-2"></i>Rejected
                        </div>
                    </div>
                </div>

                <!-- Date Range and Priority -->
                <div class="space-y-4">
                    <form method="GET" action="{{ route('agency.assignments.list') }}" class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                            <div class="flex gap-2">
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                       class="form-input flex-1">
                                <button type="submit" class="action-btn action-btn-primary">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-2">
                        <span class="text-sm font-semibold text-gray-700 mr-2">Priority:</span>
                        <div class="filter-chip" data-priority="urgent" onclick="setPriorityFilter('urgent')">
                            <span class="priority-indicator priority-urgent"></span>Urgent
                        </div>
                        <div class="filter-chip" data-priority="high" onclick="setPriorityFilter('high')">
                            <span class="priority-indicator priority-high"></span>High
                        </div>
                        <div class="filter-chip" data-priority="normal" onclick="setPriorityFilter('normal')">
                            <span class="priority-indicator priority-normal"></span>Normal
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments List -->
        @if($assignments->count() > 0)
            <div class="space-y-6" id="assignmentsList">
                @foreach($assignments as $assignment)
                    @php
                        $hoursElapsed = $assignment->assignment_Date->diffInHours(now());
                        $priority = $hoursElapsed > 48 ? 'urgent' : ($hoursElapsed > 24 ? 'high' : 'normal');
                        $statusClass = 'assignment-' . str_replace('_', '-', $assignment->assignment_Status);
                    @endphp

                    <div class="assignment-card {{ $statusClass }} bg-white rounded-lg shadow-md p-6"
                         data-assignment-id="{{ $assignment->assignment_ID }}"
                         data-status="{{ $assignment->assignment_Status }}"
                         data-priority="{{ $priority }}"
                         data-title="{{ strtolower($assignment->approval->inquiry->inquiry_Title) }}"
                         data-category="{{ $assignment->approval->inquiry->inquiry_Category }}">

                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <!-- Selection Checkbox -->
                                @if($assignment->assignment_Status === 'pending')
                                    <div class="pt-1">
                                        <input type="checkbox" class="assignment-checkbox"
                                               value="{{ $assignment->assignment_ID }}"
                                               onchange="updateBulkActions()">
                                    </div>
                                @endif

                                <!-- Priority Indicator -->
                                <div class="pt-1">
                                    <span class="priority-indicator priority-{{ $priority }}"
                                          title="{{ ucfirst($priority) }} Priority"></span>
                                </div>

                                <!-- Assignment Content -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                {{ $assignment->approval->inquiry->inquiry_Title }}
                                            </h3>

                                            <!-- Jurisdiction Badge -->
                                            @if($assignment->assignment_Status === 'pending')
                                                <span class="jurisdiction-badge jurisdiction-review-required">
                                                    <i class="fas fa-gavel mr-1"></i>Jurisdiction Review Required
                                                </span>
                                            @elseif($assignment->assignment_Status === 'in_progress' || $assignment->assignment_Status === 'completed')
                                                <span class="jurisdiction-badge jurisdiction-accepted">
                                                    <i class="fas fa-check mr-1"></i>Jurisdiction Accepted
                                                </span>
                                            @elseif($assignment->assignment_Status === 'rejected')
                                                <span class="jurisdiction-badge jurisdiction-rejected">
                                                    <i class="fas fa-times mr-1"></i>Jurisdiction Rejected
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Status Badge -->
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                'rejected' => 'bg-red-100 text-red-800 border-red-200'
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full border {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                            {{ $assignment->formatted_status }}
                                        </span>
                                    </div>

                                    <!-- Assignment Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Assignment ID:</span>
                                                <span class="font-medium">{{ $assignment->assignment_ID }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Submitter:</span>
                                                <span class="font-medium">{{ $assignment->approval->inquiry->user->name ?? 'Anonymous' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Category:</span>
                                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                                            </div>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Assigned Date:</span>
                                                <span class="font-medium">{{ $assignment->assignment_Date->format('d M Y, H:i') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Time Elapsed:</span>
                                                <span class="font-medium {{ $hoursElapsed > 48 ? 'text-red-600' : ($hoursElapsed > 24 ? 'text-orange-600' : 'text-green-600') }}">
                                                    {{ $assignment->assignment_Date->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Assigned By:</span>
                                                <span class="font-medium">{{ $assignment->assignedByStaff->staff_Name ?? 'MCMC Staff' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">Description:</h4>
                                        <p class="text-gray-700 text-sm leading-relaxed">
                                            {{ Str::limit($assignment->approval->inquiry->inquiry_Description, 200) }}
                                        </p>
                                    </div>

                                    <!-- MCMC Comments -->
                                    @if($assignment->assignment_Comments)
                                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <h4 class="font-semibold text-blue-900 mb-2">MCMC Comments:</h4>
                                            <p class="text-blue-800 text-sm">{{ $assignment->assignment_Comments }}</p>
                                        </div>
                                    @endif

                                    <!-- Rejection Reason -->
                                    @if($assignment->rejection_Reason)
                                        <div class="mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <h4 class="font-semibold text-red-900 mb-2">Rejection Reason:</h4>
                                            <p class="text-red-800 text-sm">{{ $assignment->rejection_Reason }}</p>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                        <div class="text-sm text-gray-500">
                                            @if($assignment->completed_At)
                                                Completed: {{ $assignment->completed_At->format('d M Y, H:i') }}
                                            @else
                                                Expected response: {{ $assignment->assignment_Date->addHours(48)->format('d M Y, H:i') }}
                                            @endif
                                        </div>

                                        <div class="assignment-actions">
                                            @if($assignment->assignment_Status === 'pending')
                                                <a href="{{ route('agency.assignments.jurisdiction-review', $assignment->assignment_ID) }}"
                                                   class="action-btn action-btn-danger">
                                                    <i class="fas fa-gavel mr-2"></i>Review Jurisdiction
                                                </a>
                                            @else
                                                <a href="{{ route('agency.assignments.details', $assignment->assignment_ID) }}"
                                                   class="action-btn action-btn-outline">
                                                    <i class="fas fa-eye mr-2"></i>View Details
                                                </a>
                                            @endif

                                            @if($assignment->canBeUpdated())
                                                <a href="{{ route('agency.assignments.details', $assignment->assignment_ID) }}"
                                                   class="action-btn action-btn-primary">
                                                    <i class="fas fa-edit mr-2"></i>Update Status
                                                </a>
                                            @endif

                                            @if($assignment->approval->inquiry->inquiry_Supporting_Documents)
                                                <a href="{{ route('inquiry.attachment', $assignment->approval->inquiry->inquiry_ID) }}"
                                                   class="action-btn action-btn-outline">
                                                    <i class="fas fa-paperclip mr-2"></i>Attachment
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md">
                <div class="text-center py-12">
                    <i class="fas fa-tasks text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 text-lg">No assignments found</p>
                    <p class="text-gray-400 text-sm">You don't have any assignments matching the current filters</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Confirmation Modal -->
<div id="bulkActionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="bulkActionTitle">Confirm Bulk Action</h3>
            <p class="text-gray-700 mb-6" id="bulkActionMessage">Are you sure you want to perform this action?</p>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkActionModal()" class="action-btn action-btn-outline">
                    Cancel
                </button>
                <button type="button" onclick="confirmBulkAction()" class="action-btn action-btn-primary" id="confirmBulkActionBtn">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentFilter = 'all';
let currentPriority = 'all';
let selectedAssignments = [];
let bulkActionType = '';

// Filter functions
function setStatusFilter(status) {
    currentFilter = status;
    document.querySelectorAll('[data-filter]').forEach(chip => {
        chip.classList.remove('active');
    });
    document.querySelector(`[data-filter="${status}"]`).classList.add('active');
    filterAssignments();
}

function setPriorityFilter(priority) {
    currentPriority = currentPriority === priority ? 'all' : priority;
    document.querySelectorAll('[data-priority]').forEach(chip => {
        chip.classList.toggle('active', chip.dataset.priority === priority && currentPriority === priority);
    });
    filterAssignments();
}

function filterAssignments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const assignments = document.querySelectorAll('.assignment-card');

    assignments.forEach(assignment => {
        const status = assignment.dataset.status;
        const priority = assignment.dataset.priority;
        const title = assignment.dataset.title;
        const category = assignment.dataset.category;

        const statusMatch = currentFilter === 'all' || status === currentFilter;
        const priorityMatch = currentPriority === 'all' || priority === currentPriority;
        const searchMatch = searchTerm === '' || title.includes(searchTerm) || category.includes(searchTerm);

        assignment.style.display = statusMatch && priorityMatch && searchMatch ? 'block' : 'none';
    });
}

// Bulk actions
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.assignment-checkbox:checked');
    const count = checkboxes.length;

    selectedAssignments = Array.from(checkboxes).map(cb => cb.value);

    document.getElementById('selectedCount').textContent = `${count} assignment${count !== 1 ? 's' : ''} selected`;
    document.getElementById('bulkActions').style.display = count > 0 ? 'block' : 'none';
}

function clearSelection() {
    document.querySelectorAll('.assignment-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateBulkActions();
}

function bulkAccept() {
    if (selectedAssignments.length === 0) return;

    bulkActionType = 'accept';
    document.getElementById('bulkActionTitle').textContent = 'Accept Assignments';
    document.getElementById('bulkActionMessage').textContent =
        `Are you sure you want to accept ${selectedAssignments.length} assignment${selectedAssignments.length !== 1 ? 's' : ''}? This will confirm jurisdiction for all selected assignments.`;
    document.getElementById('confirmBulkActionBtn').textContent = 'Accept All';
    document.getElementById('confirmBulkActionBtn').className = 'action-btn action-btn-success';
    document.getElementById('bulkActionModal').classList.remove('hidden');
}

function bulkReject() {
    if (selectedAssignments.length === 0) return;

    bulkActionType = 'reject';
    document.getElementById('bulkActionTitle').textContent = 'Reject Assignments';
    document.getElementById('bulkActionMessage').textContent =
        `Are you sure you want to reject ${selectedAssignments.length} assignment${selectedAssignments.length !== 1 ? 's' : ''}? This will return them to MCMC for reassignment.`;
    document.getElementById('confirmBulkActionBtn').textContent = 'Reject All';
    document.getElementById('confirmBulkActionBtn').className = 'action-btn action-btn-danger';
    document.getElementById('bulkActionModal').classList.remove('hidden');
}

function confirmBulkAction() {
    // Here you would implement the actual bulk action logic
    console.log(`Performing bulk ${bulkActionType} on assignments:`, selectedAssignments);

    // Simulate action
    selectedAssignments.forEach(assignmentId => {
        const card = document.querySelector(`[data-assignment-id="${assignmentId}"]`);
        if (card) {
            const statusBadge = card.querySelector('.rounded-full');
            if (bulkActionType === 'accept') {
                statusBadge.className = 'px-3 py-1 text-sm font-semibold rounded-full border bg-blue-100 text-blue-800 border-blue-200';
                statusBadge.textContent = 'In Progress';
                card.className = card.className.replace('assignment-pending', 'assignment-in-progress');
            } else if (bulkActionType === 'reject') {
                statusBadge.className = 'px-3 py-1 text-sm font-semibold rounded-full border bg-red-100 text-red-800 border-red-200';
                statusBadge.textContent = 'Rejected';
                card.className = card.className.replace('assignment-pending', 'assignment-rejected');
            }
        }
    });

    closeBulkActionModal();
    clearSelection();
    showToast(`Bulk ${bulkActionType} completed successfully`, 'success');
}

function closeBulkActionModal() {
    document.getElementById('bulkActionModal').classList.add('hidden');
}

// Utility functions
function toggleExportMenu() {
    document.getElementById('exportMenu').classList.toggle('hidden');
}

function refreshAssignments() {
    window.location.reload();
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const exportMenu = document.getElementById('exportMenu');
    const exportButton = event.target.closest('button');

    if (!exportButton || !exportButton.onclick || exportButton.onclick.toString().indexOf('toggleExportMenu') === -1) {
        exportMenu.classList.add('hidden');
    }
});

// Initialize filters
document.addEventListener('DOMContentLoaded', function() {
    // Set initial filter state based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');

    if (statusParam) {
        setStatusFilter(statusParam);
    }
});
</script>
@endpush
