@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
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
<div class="flex justify-center items-start py-8" style="color:#000 !important;">
    <div class="w-full max-w-4xl">
        <div class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 shadow-lg p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
                <div class="flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-white mb-0 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-white"></i>Assignment Details
                    </h4>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-400 text-yellow-900',
                            'in_progress' => 'bg-blue-400 text-blue-900',
                            'completed' => 'bg-green-400 text-green-900',
                            'rejected' => 'bg-red-400 text-red-900'
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-400 text-gray-900' }}">
                        {{ $assignment->formatted_status }}
                    </span>
                </div>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <!-- Assignment Information -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Assignment Information</div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assignment ID:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignment_ID }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assignment Date:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignment_Date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assigned By:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignedByStaff->staff_Name ?? 'N/A' }}</span>
                            </div>
                            @if($assignment->completed_At)
                                <div>
                                    <span class="font-bold" style="color:#000 !important;">Completed At:</span>
                                    <span style="color:#6b7280;">{{ $assignment->completed_At->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inquiry Details -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Inquiry Details</div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Inquiry ID:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->inquiry_ID }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Submitted By:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">User Email:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->user->email ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Category:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Submission Date:</span>
                                <span style="color:#6b7280;">{{ \Carbon\Carbon::parse($assignment->approval->inquiry->inquiry_Created_At)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Inquiry Status:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Status)) }}</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <span class="font-bold" style="color:#000 !important;">Subject:</span>
                            <p style="color:#6b7280;" class="mt-1">{{ $assignment->approval->inquiry->inquiry_Title }}</p>
                        </div>
                        <div class="mb-4">
                            <span class="font-bold" style="color:#000 !important;">Description:</span>
                            <p style="color:#6b7280;" class="mt-1">{{ $assignment->approval->inquiry->inquiry_Description }}</p>
                        </div>
                        @if($assignment->approval->inquiry->inquiry_Attachment_URL)
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                                <a href="{{ route('inquiry.attachment', $assignment->approval->inquiry->inquiry_ID) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm ml-2 text-black" style="color:#000 !important;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- MCMC Comments -->
                @if($assignment->assignment_Comments)
                    <div class="mb-6">
                        <div class="mb-4 font-bold text-lg" style="color:#000 !important;">MCMC Comments</div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p style="color:#065f46;">{{ $assignment->assignment_Comments }}</p>
                        </div>
                    </div>
                @endif

                <!-- Rejection Reason -->
                @if($assignment->rejection_Reason)
                    <div class="mb-6">
                        <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Rejection Reason</div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p style="color:#7f1d1d;">{{ $assignment->rejection_Reason }}</p>
                        </div>
                    </div>
                @endif

                <!-- Jurisdiction Review Section -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Jurisdiction Review</div>
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-gavel text-yellow-600 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-yellow-800 mb-2">Action Required</h5>
                                <p class="text-yellow-700 text-sm mb-3">
                                    Please review this inquiry to determine if it falls within your agency's jurisdiction.
                                    If it does, proceed with the verification process. If not, please reject the assignment
                                    and provide a clear reason why it's outside your authority.
                                </p>
                                <ul class="text-yellow-700 text-sm list-disc list-inside space-y-1">
                                    <li>Review the inquiry details and supporting evidence carefully</li>
                                    <li>Check if the matter falls under your agency's regulatory scope</li>
                                    <li>If accepting, proceed with your standard verification process</li>
                                    <li>If rejecting, provide specific reasons for MCMC to reassign appropriately</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-6">
                    @if($assignment->canBeUpdated())
                        <button onclick="openStatusModal()" class="btn btn-success px-6 py-2 text-white">
                            <i class="fas fa-check mr-2"></i>Accept & Start Review
                        </button>
                        <button onclick="openRejectModal()" class="btn btn-danger px-6 py-2 text-white">
                            <i class="fas fa-times mr-2"></i>Reject Assignment
                        </button>
                        @if($assignment->assignment_Status === 'in_progress')
                            <button onclick="openCompleteModal()" class="btn btn-primary px-6 py-2 text-white">
                                <i class="fas fa-flag-checkered mr-2"></i>Complete Review
                            </button>
                        @endif
                    @endif
                    <a href="{{ route('agency.assignments.list') }}" class="btn btn-secondary px-6 py-2 text-white">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Assignments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modals -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="in_progress">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Accept Assignment
                </h3>

                <p class="text-gray-600 mb-4">You are about to accept this assignment and start the review process.</p>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2" style="color:#000 !important;">Comments</label>
                    <textarea name="comments" rows="3" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Add any comments about starting the review (optional)"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeStatusModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 text-white">
                        <i class="fas fa-check mr-2"></i>Accept & Start Review
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="rejected">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>Reject Assignment
                </h3>

                <p class="text-gray-600 mb-4">Please explain why this inquiry is outside your jurisdiction.</p>

                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-bold mb-2" style="color:#000 !important;">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="4" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Please provide a detailed explanation..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 py-2 text-white">
                        <i class="fas fa-times mr-2"></i>Reject Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="completed">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-flag-checkered text-blue-500 mr-2"></i>Complete Review
                </h3>

                <p class="text-gray-600 mb-4">Mark this assignment as completed and provide your findings.</p>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2" style="color:#000 !important;">
                        Review Findings & Comments <span class="text-red-500">*</span>
                    </label>
                    <textarea name="comments" rows="4" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Please provide your review findings and any recommendations..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeCompleteModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 text-white">
                        <i class="fas fa-flag-checkered mr-2"></i>Complete Review
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

// Close modals when clicking outside
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
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
<div class="flex justify-center items-start py-8" style="color:#000 !important;">
    <div class="w-full max-w-4xl">
        <div class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 shadow-lg p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
                <div class="flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-white mb-0 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-white"></i>Assignment Details
                    </h4>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-400 text-yellow-900',
                            'in_progress' => 'bg-blue-400 text-blue-900',
                            'completed' => 'bg-green-400 text-green-900',
                            'rejected' => 'bg-red-400 text-red-900'
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$assignment->assignment_Status] ?? 'bg-gray-400 text-gray-900' }}">
                        {{ $assignment->formatted_status }}
                    </span>
                </div>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <!-- Assignment Information -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Assignment Information</div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assignment ID:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignment_ID }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assignment Date:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignment_Date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Assigned By:</span>
                                <span style="color:#6b7280;">{{ $assignment->assignedByStaff->staff_Name ?? 'N/A' }}</span>
                            </div>
                            @if($assignment->completed_At)
                                <div>
                                    <span class="font-bold" style="color:#000 !important;">Completed At:</span>
                                    <span style="color:#6b7280;">{{ $assignment->completed_At->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Inquiry Details -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Inquiry Details</div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Inquiry ID:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->inquiry_ID }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Submitted By:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">User Email:</span>
                                <span style="color:#6b7280;">{{ $assignment->approval->inquiry->user->email ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Category:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Submission Date:</span>
                                <span style="color:#6b7280;">{{ \Carbon\Carbon::parse($assignment->approval->inquiry->inquiry_Created_At)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Inquiry Status:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Status)) }}</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <span class="font-bold" style="color:#000 !important;">Subject:</span>
                            <p style="color:#6b7280;" class="mt-1">{{ $assignment->approval->inquiry->inquiry_Title }}</p>
                        </div>
                        <div class="mb-4">
                            <span class="font-bold" style="color:#000 !important;">Description:</span>
                            <p style="color:#6b7280;" class="mt-1">{{ $assignment->approval->inquiry->inquiry_Description }}</p>
                        </div>
                        @if($assignment->approval->inquiry->inquiry_Attachment_URL)
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                                <a href="{{ route('inquiry.attachment', $assignment->approval->inquiry->inquiry_ID) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm ml-2 text-black" style="color:#000 !important;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- MCMC Comments -->
                @if($assignment->assignment_Comments)
                    <div class="mb-6">
                        <div class="mb-4 font-bold text-lg" style="color:#000 !important;">MCMC Comments</div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p style="color:#065f46;">{{ $assignment->assignment_Comments }}</p>
                        </div>
                    </div>
                @endif

                <!-- Rejection Reason -->
                @if($assignment->rejection_Reason)
                    <div class="mb-6">
                        <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Rejection Reason</div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p style="color:#7f1d1d;">{{ $assignment->rejection_Reason }}</p>
                        </div>
                    </div>
                @endif

                <!-- Jurisdiction Review Section -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Jurisdiction Review</div>
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-gavel text-yellow-600 mr-3 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-yellow-800 mb-2">Action Required</h5>
                                <p class="text-yellow-700 text-sm mb-3">
                                    Please review this inquiry to determine if it falls within your agency's jurisdiction.
                                    If it does, proceed with the verification process. If not, please reject the assignment
                                    and provide a clear reason why it's outside your authority.
                                </p>
                                <ul class="text-yellow-700 text-sm list-disc list-inside space-y-1">
                                    <li>Review the inquiry details and supporting evidence carefully</li>
                                    <li>Check if the matter falls under your agency's regulatory scope</li>
                                    <li>If accepting, proceed with your standard verification process</li>
                                    <li>If rejecting, provide specific reasons for MCMC to reassign appropriately</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-6">
                    @if($assignment->canBeUpdated())
                        <button onclick="openStatusModal()" class="btn btn-success px-6 py-2 text-white">
                            <i class="fas fa-check mr-2"></i>Accept & Start Review
                        </button>
                        <button onclick="openRejectModal()" class="btn btn-danger px-6 py-2 text-white">
                            <i class="fas fa-times mr-2"></i>Reject Assignment
                        </button>
                        @if($assignment->assignment_Status === 'in_progress')
                            <button onclick="openCompleteModal()" class="btn btn-primary px-6 py-2 text-white">
                                <i class="fas fa-flag-checkered mr-2"></i>Complete Review
                            </button>
                        @endif
                    @endif
                    <a href="{{ route('agency.assignments.list') }}" class="btn btn-secondary px-6 py-2 text-white">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Assignments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modals -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="in_progress">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Accept Assignment
                </h3>

                <p class="text-gray-600 mb-4">You are about to accept this assignment and start the review process.</p>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2" style="color:#000 !important;">Comments</label>
                    <textarea name="comments" rows="3" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Add any comments about starting the review (optional)"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeStatusModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 text-white">
                        <i class="fas fa-check mr-2"></i>Accept & Start Review
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="rejected">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>Reject Assignment
                </h3>

                <p class="text-gray-600 mb-4">Please explain why this inquiry is outside your jurisdiction.</p>

                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-bold mb-2" style="color:#000 !important;">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="4" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Please provide a detailed explanation..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger px-4 py-2 text-white">
                        <i class="fas fa-times mr-2"></i>Reject Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="completed">

            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-flag-checkered text-blue-500 mr-2"></i>Complete Review
                </h3>

                <p class="text-gray-600 mb-4">Mark this assignment as completed and provide your findings.</p>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2" style="color:#000 !important;">
                        Review Findings & Comments <span class="text-red-500">*</span>
                    </label>
                    <textarea name="comments" rows="4" class="form-input w-full text-black" style="color:#000 !important;"
                              placeholder="Please provide your review findings and any recommendations..." required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeCompleteModal()" class="btn btn-secondary px-4 py-2 text-white">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 text-white">
                        <i class="fas fa-flag-checkered mr-2"></i>Complete Review
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}

// Close modals when clicking outside
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush
