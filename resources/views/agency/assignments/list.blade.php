@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    My Assignments - MySebenarnya
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
    <a href="{{ route('agency.profile') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('agency.settings') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
    <div class="py-8" style="color:#000 !important;">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">My Assignments</h1>
                <div class="flex gap-4">
                    @php
                        $pendingCount = $assignments->where('assignment_Status', 'pending')->count();
                        $inProgressCount = $assignments->where('assignment_Status', 'in_progress')->count();
                    @endphp
                    <div class="flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="text-sm font-semibold">{{ $pendingCount }} Pending</span>
                    </div>
                    <div class="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        <i class="fas fa-spinner mr-2"></i>
                        <span class="text-sm font-semibold">{{ $inProgressCount }} In Progress</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('agency.assignments.list') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Status</label>
                        <select id="status" name="status" class="form-select w-full text-black"
                            style="color:#000 !important;">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-bold mb-2" style="color:#000 !important;">Start
                            Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="form-input w-full text-black" style="color:#000 !important;">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-bold mb-2" style="color:#000 !important;">End
                            Date</label>
                        <div class="flex gap-2">
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="form-input flex-1 text-black" style="color:#000 !important;">
                            <button type="submit" class="btn btn-primary px-4 py-2 text-white">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Assignments Cards -->
            @if ($assignments->count() > 0)
                <div class="space-y-6">
                    @foreach ($assignments as $assignment)
                        <div
                            class="bg-white rounded-lg shadow-md border-l-4 {{ $assignment->assignment_Status === 'pending'
                                ? 'border-yellow-400'
                                : ($assignment->assignment_Status === 'in_progress'
                                    ? 'border-blue-400'
                                    : ($assignment->assignment_Status === 'completed'
                                        ? 'border-green-400'
                                        : 'border-red-400')) }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $assignment->approval->inquiry->inquiry_Title }}
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="font-semibold text-gray-700">Inquiry ID:</span>
                                                <span
                                                    class="text-gray-600">{{ $assignment->approval->inquiry->inquiry_ID }}</span>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">User:</span>
                                                <span
                                                    class="text-gray-600">{{ $assignment->approval->inquiry->user->name ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Category:</span>
                                                <span
                                                    class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Assigned Date:</span>
                                                <span
                                                    class="text-gray-600">{{ $assignment->assignment_Date->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-6 flex flex-col items-end space-y-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $assignment->formatted_status }}
                                        </span>
                                        @if ($assignment->assignment_Status === 'pending')
                                            <span class="text-xs text-red-600 font-medium">
                                                <i class="fas fa-clock mr-1"></i>Action Required
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h4 class="font-semibold text-gray-900 mb-2">Description:</h4>
                                    <p class="text-gray-700 text-sm">
                                        {{ $assignment->approval->inquiry->inquiry_Description }}</p>
                                </div>

                                @if ($assignment->assignment_Comments)
                                    <div class="mb-4 bg-blue-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-blue-900 mb-2">MCMC Comments:</h4>
                                        <p class="text-blue-800 text-sm">{{ $assignment->assignment_Comments }}</p>
                                    </div>
                                @endif

                                @if ($assignment->rejection_Reason)
                                    <div class="mb-4 bg-red-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-red-900 mb-2">Rejection Reason:</h4>
                                        <p class="text-red-800 text-sm">{{ $assignment->rejection_Reason }}</p>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-4 border-t">
                                    <div class="text-sm text-gray-500">
                                        Assigned by: {{ $assignment->assignedByStaff->staff_Name ?? 'N/A' }}
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('agency.assignments.details', $assignment->assignment_ID) }}"
                                            class="btn btn-outline-primary btn-sm px-3 py-1 text-black"
                                            style="color:#000 !important;">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </a>
                                        @if ($assignment->canBeUpdated())
                                            <button
                                                onclick="openStatusModal({{ $assignment->assignment_ID }}, '{{ $assignment->assignment_Status }}')"
                                                class="btn btn-primary btn-sm px-3 py-1 text-white">
                                                <i class="fas fa-edit mr-1"></i>Update Status
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $assignments->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md">
                    <div class="text-center py-12">
                        <i class="fas fa-tasks text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 text-lg">No assignments found</p>
                        <p class="text-gray-400 text-sm">You don't have any assignments yet</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Status Update Modal with Review Process -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Update Assignment Status</h3>

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-bold mb-2" style="color:#000 !important;">Status
                            <span class="text-red-500">*</span></label>
                        <select id="modal_status" name="status" class="form-select w-full text-black"
                            style="color:#000 !important;" required>
                            <option value="">Select Status</option>
                            <option value="in_progress">Accept & Start Review</option>
                            <option value="completed">Complete Review</option>
                            <option value="rejected">Reject (Outside Jurisdiction)</option>
                        </select>
                    </div>

                    <!-- Review Progress Section -->
                    <div id="reviewProgressDiv" class="mb-4 hidden">
                        <label class="block text-sm font-bold mb-2" style="color:#000 !important;">Review Progress</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="initial_review" name="review_steps[]" value="initial_review"
                                    class="mr-2">
                                <label for="initial_review" class="text-sm">Initial assessment completed</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="documentation_review" name="review_steps[]"
                                    value="documentation_review" class="mr-2">
                                <label for="documentation_review" class="text-sm">Documentation review completed</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="compliance_check" name="review_steps[]"
                                    value="compliance_check" class="mr-2">
                                <label for="compliance_check" class="text-sm">Compliance check completed</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comments" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Comments</label>
                        <textarea id="modal_comments" name="comments" rows="3" class="form-input w-full text-black"
                            style="color:#000 !important;" placeholder="Add your comments (optional)"></textarea>
                    </div>

                    <div id="rejectionReasonDiv" class="mb-4 hidden">
                        <label for="rejection_reason" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Rejection Reason <span class="text-red-500">*</span></label>
                        <textarea id="modal_rejection_reason" name="rejection_reason" rows="3" class="form-input w-full text-black"
                            style="color:#000 !important;" placeholder="Please explain why this inquiry is outside your jurisdiction"></textarea>
                    </div>

                    <div id="completionSummaryDiv" class="mb-4 hidden">
                        <label for="completion_summary" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Review Summary <span class="text-red-500">*</span></label>
                        <textarea id="modal_completion_summary" name="completion_summary" rows="4"
                            class="form-input w-full text-black" style="color:#000 !important;"
                            placeholder="Provide a summary of your review findings and recommendations"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeStatusModal()"
                            class="btn btn-secondary px-4 py-2 text-white">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 text-white">
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
        function openStatusModal(assignmentId, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const statusSelect = document.getElementById('modal_status');
            const rejectionDiv = document.getElementById('rejectionReasonDiv');
            const completionDiv = document.getElementById('completionSummaryDiv');
            const reviewProgressDiv = document.getElementById('reviewProgressDiv');

            form.action = `{{ url('/agency/assignments') }}/${assignmentId}/update-status`;

            // Reset form
            form.reset();
            rejectionDiv.classList.add('hidden');
            completionDiv.classList.add('hidden');
            reviewProgressDiv.classList.add('hidden');

            // Pre-populate based on current status
            if (currentStatus === 'pending') {
                // For pending assignments, they can accept or reject
                statusSelect.innerHTML = `
            <option value="">Select Status</option>
            <option value="in_progress">Accept & Start Review</option>
            <option value="rejected">Reject (Outside Jurisdiction)</option>
        `;
            } else if (currentStatus === 'in_progress') {
                // For in-progress assignments, they can complete or reject
                statusSelect.innerHTML = `
            <option value="">Select Status</option>
            <option value="completed">Complete Review</option>
            <option value="rejected">Reject (Outside Jurisdiction)</option>
        `;
            }

            modal.classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        document.getElementById('modal_status').addEventListener('change', function() {
            const rejectionDiv = document.getElementById('rejectionReasonDiv');
            const rejectionTextarea = document.getElementById('modal_rejection_reason');
            const completionDiv = document.getElementById('completionSummaryDiv');
            const completionTextarea = document.getElementById('modal_completion_summary');
            const reviewProgressDiv = document.getElementById('reviewProgressDiv');

            // Reset all fields
            rejectionDiv.classList.add('hidden');
            completionDiv.classList.add('hidden');
            reviewProgressDiv.classList.add('hidden');
            rejectionTextarea.required = false;
            completionTextarea.required = false;

            if (this.value === 'rejected') {
                rejectionDiv.classList.remove('hidden');
                rejectionTextarea.required = true;
            } else if (this.value === 'completed') {
                completionDiv.classList.remove('hidden');
                completionTextarea.required = true;
            } else if (this.value === 'in_progress') {
                reviewProgressDiv.classList.remove('hidden');
            }
        });

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        // Handle form submission
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            const status = document.getElementById('modal_status').value;

            if (status === 'completed') {
                const summary = document.getElementById('modal_completion_summary').value;
                if (!summary.trim()) {
                    e.preventDefault();
                    alert('Please provide a review summary before completing the assignment.');
                    return;
                }
            }

            if (status === 'rejected') {
                const reason = document.getElementById('modal_rejection_reason').value;
                if (!reason.trim()) {
                    e.preventDefault();
                    alert('Please provide a rejection reason.');
                    return;
                }
            }
        });
    </script>
@endpush
