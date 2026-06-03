@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .assignment-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 1.5rem;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #e5e7eb;
        }
        .timeline-item:last-child:before {
            display: none;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            z-index: 10;
        }
        .jurisdiction-alert {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
        }
        .action-required {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .details-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .details-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .action-button {
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
@endpush

@section('title')
    Assignment Details - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('agency.assignments.list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        My Assignments
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="{{ route('agency.profile') }}" class="nav-link">
        <i class="fas fa-user mr-2" aria-hidden="true"></i>
        Profile
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
                <h1 class="text-2xl font-bold text-gray-900">Assignment Details</h1>
                <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                    <span><i class="fas fa-calendar mr-1"></i>{{ $assignment->assignment_Date->format('d M Y, H:i') }}</span>
                    <span><i class="fas fa-user mr-1"></i>{{ $assignment->assignedByStaff->staff_Name ?? 'MCMC Staff' }}</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('agency.assignments.list') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Assignments
                </a>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800'
                    ];
                @endphp
                <span class="assignment-status-badge {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-100 text-gray-800' }}">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    {{ $assignment->formatted_status }}
                </span>
            </div>
        </div>

        <!-- Jurisdiction Alert for Pending Assignments -->
        @if($assignment->assignment_Status === 'pending')
            <div class="jurisdiction-alert p-4 rounded-lg mb-8 action-required">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-gavel text-yellow-600 text-xl mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-yellow-900 text-lg">Jurisdiction Review Required</h3>
                        <p class="text-yellow-800 mt-1">
                            This assignment requires your review to determine if it falls within your agency's jurisdiction.
                            Please review the inquiry details and either accept or reject the assignment.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('agency.assignments.jurisdiction-review', $assignment->assignment_ID) }}"
                               class="action-button bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                                <i class="fas fa-gavel mr-2"></i>Review Jurisdiction
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Assignment Progress -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">
                <!-- Inquiry Information Card -->
                <div class="details-card bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $assignment->approval->inquiry->inquiry_Title }}</h2>
                        <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                            ID: {{ $assignment->approval->inquiry->inquiry_ID }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Submitter Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium">{{ $assignment->approval->inquiry->user->name ?? 'Anonymous' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $assignment->approval->inquiry->user->email ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Submission Date:</span>
                                    <span class="font-medium">{{ $assignment->approval->inquiry->inquiry_Created_At->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Inquiry Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Category:</span>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Priority:</span>
                                    <span class="font-medium text-orange-600">
                                        @if($assignment->assignment_Date->diffInHours(now()) > 48)
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                        @elseif($assignment->assignment_Date->diffInHours(now()) > 24)
                                            <i class="fas fa-clock mr-1"></i>High
                                        @else
                                            <i class="fas fa-check-circle mr-1"></i>Normal
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Status)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Description</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 leading-relaxed">{{ $assignment->approval->inquiry->inquiry_Description }}</p>
                        </div>
                    </div>

                    @if($assignment->approval->inquiry->inquiry_Supporting_Documents)
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Supporting Documents</h4>
                            <div class="flex items-center space-x-2 text-sm">
                                <i class="fas fa-paperclip text-blue-500"></i>
                                <a href="{{ route('inquiry.attachment', $assignment->approval->inquiry->inquiry_ID) }}"
                                   class="text-blue-600 hover:underline">
                                    View Attachment
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($assignment->assignment_Comments)
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">MCMC Comments</h4>
                            <p class="text-blue-800 text-sm">{{ $assignment->assignment_Comments }}</p>
                        </div>
                    @endif
                </div>

                <!-- Assignment Actions Card -->
                @if($assignment->canBeUpdated())
                    <div class="details-card bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($assignment->assignment_Status === 'pending')
                                <div class="p-4 border-2 border-green-200 rounded-lg bg-green-50">
                                    <h4 class="font-semibold text-green-900 mb-2">Accept Assignment</h4>
                                    <p class="text-green-800 text-sm mb-3">Confirm jurisdiction and start review process</p>
                                    <a href="{{ route('agency.assignments.jurisdiction-review', $assignment->assignment_ID) }}"
                                       class="action-button bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center text-sm">
                                        <i class="fas fa-check mr-2"></i>Review & Accept
                                    </a>
                                </div>
                                <div class="p-4 border-2 border-red-200 rounded-lg bg-red-50">
                                    <h4 class="font-semibold text-red-900 mb-2">Reject Assignment</h4>
                                    <p class="text-red-800 text-sm mb-3">Return to MCMC if outside jurisdiction</p>
                                    <a href="{{ route('agency.assignments.jurisdiction-review', $assignment->assignment_ID) }}"
                                       class="action-button bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center text-sm">
                                        <i class="fas fa-times mr-2"></i>Review & Reject
                                    </a>
                                </div>
                            @elseif($assignment->assignment_Status === 'in_progress')
                                <div class="p-4 border-2 border-blue-200 rounded-lg bg-blue-50">
                                    <h4 class="font-semibold text-blue-900 mb-2">Complete Review</h4>
                                    <p class="text-blue-800 text-sm mb-3">Mark assignment as completed</p>
                                    <button onclick="openStatusModal('completed')"
                                            class="action-button bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center text-sm">
                                        <i class="fas fa-check-circle mr-2"></i>Complete
                                    </button>
                                </div>
                                <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                                    <h4 class="font-semibold text-gray-900 mb-2">Add Progress Update</h4>
                                    <p class="text-gray-800 text-sm mb-3">Update assignment status and comments</p>
                                    <button onclick="openStatusModal('in_progress')"
                                            class="action-button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg inline-flex items-center text-sm">
                                        <i class="fas fa-edit mr-2"></i>Update
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Assignment Timeline -->
                <div class="details-card bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Timeline</h3>
                    <div class="space-y-4">
                        <div class="timeline-item">
                            <div class="timeline-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="pl-4">
                                <h4 class="font-semibold text-gray-900 text-sm">Assignment Created</h4>
                                <p class="text-gray-600 text-sm">{{ $assignment->assignment_Date->format('d M Y, H:i') }}</p>
                                <p class="text-gray-500 text-xs">Assigned by {{ $assignment->assignedByStaff->staff_Name ?? 'MCMC Staff' }}</p>
                            </div>
                        </div>

                        @if($assignment->assignment_Status === 'in_progress')
                            <div class="timeline-item">
                                <div class="timeline-icon bg-green-100 text-green-600">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="pl-4">
                                    <h4 class="font-semibold text-gray-900 text-sm">Review Started</h4>
                                    <p class="text-gray-600 text-sm">Assignment accepted and review in progress</p>
                                </div>
                            </div>
                        @elseif($assignment->assignment_Status === 'completed')
                            <div class="timeline-item">
                                <div class="timeline-icon bg-green-100 text-green-600">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="pl-4">
                                    <h4 class="font-semibold text-gray-900 text-sm">Review Started</h4>
                                    <p class="text-gray-600 text-sm">Assignment accepted</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon bg-blue-100 text-blue-600">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="pl-4">
                                    <h4 class="font-semibold text-gray-900 text-sm">Review Completed</h4>
                                    <p class="text-gray-600 text-sm">{{ $assignment->completed_At ? $assignment->completed_At->format('d M Y, H:i') : 'Recently' }}</p>
                                </div>
                            </div>
                        @elseif($assignment->assignment_Status === 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-icon bg-red-100 text-red-600">
                                    <i class="fas fa-times"></i>
                                </div>
                                <div class="pl-4">
                                    <h4 class="font-semibold text-gray-900 text-sm">Assignment Rejected</h4>
                                    <p class="text-gray-600 text-sm">Returned to MCMC for reassignment</p>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-icon bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="pl-4">
                                    <h4 class="font-semibold text-gray-900 text-sm">Awaiting Review</h4>
                                    <p class="text-gray-600 text-sm">Pending jurisdiction review</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assignment Statistics -->
                <div class="details-card bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Info</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Assignment ID</span>
                            <span class="font-semibold text-sm">{{ $assignment->assignment_ID }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Time Elapsed</span>
                            <span class="font-semibold text-sm">{{ $assignment->assignment_Date->diffForHumans() }}</span>
                        </div>
                        @if($assignment->completed_At)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Completion Time</span>
                                <span class="font-semibold text-sm">{{ $assignment->assignment_Date->diffInHours($assignment->completed_At) }}h</span>
                            </div>
                        @else
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Expected Response</span>
                                <span class="font-semibold text-sm text-orange-600">{{ $assignment->assignment_Date->addHours(48)->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($assignment->rejection_Reason)
                    <!-- Rejection Details -->
                    <div class="details-card bg-red-50 border border-red-200 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">Rejection Details</h3>
                        <p class="text-red-800 text-sm">{{ $assignment->rejection_Reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="statusForm" method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Update Assignment Status</h3>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-bold mb-2">Status <span class="text-red-500">*</span></label>
                    <select id="modal_status" name="status" class="form-select w-full" required>
                        <option value="">Select Status</option>
                        <option value="completed">Complete Review</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2">Comments <span class="text-red-500">*</span></label>
                    <textarea id="modal_comments" name="comments" rows="4"
                              class="form-input w-full"
                              placeholder="Please provide details about your review findings..."
                              required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeStatusModal()" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Update Status
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openStatusModal(status) {
    const modal = document.getElementById('statusModal');
    const statusSelect = document.getElementById('modal_status');

    if (status === 'completed') {
        statusSelect.innerHTML = '<option value="completed" selected>Complete Review</option>';
    } else {
        statusSelect.innerHTML = '<option value="in_progress" selected>Update Progress</option>';
    }

    modal.classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('statusForm').reset();
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Auto-resize textarea
document.getElementById('modal_comments').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
</script>
@endpush
