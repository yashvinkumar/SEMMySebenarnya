@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <style>
        .jurisdiction-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: border-color 0.3s ease;
        }
        .jurisdiction-card:hover {
            border-color: #3b82f6;
        }
        .jurisdiction-option {
            cursor: pointer;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .jurisdiction-option:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        .jurisdiction-option.selected {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        .jurisdiction-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
        }
        .jurisdiction-accept {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-left: 4px solid #10b981;
        }
        .jurisdiction-reject {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
        }
    </style>
@endpush

@section('title')
    Jurisdiction Review - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('agency.assignments.list') }}" class="nav-link">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        My Assignments
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="#" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-gavel mr-2" aria-hidden="true"></i>
        Jurisdiction Review
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
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Jurisdiction Review</h1>
                    <p class="text-gray-600 mt-2">Review the inquiry to determine if it falls within your agency's jurisdiction</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('agency.assignments.list') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Assignments
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full text-sm font-semibold">
                        1
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-blue-600">Review Assignment</p>
                        <p class="text-xs text-gray-500">Analyze inquiry details</p>
                    </div>
                </div>
                <div class="flex-1 mx-4">
                    <div class="h-1 bg-gray-200 rounded">
                        <div class="h-1 bg-blue-500 rounded" style="width: 50%;"></div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full text-sm font-semibold">
                        2
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-600">Jurisdiction Decision</p>
                        <p class="text-xs text-gray-500">Accept or reject</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Details Card -->
        <div class="jurisdiction-card bg-white p-6 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $assignment->approval->inquiry->inquiry_Title }}</h2>
                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                        <span><i class="fas fa-calendar mr-1"></i>{{ $assignment->assignment_Date->format('d M Y, H:i') }}</span>
                        <span><i class="fas fa-user mr-1"></i>{{ $assignment->approval->inquiry->user->name ?? 'Anonymous' }}</span>
                        <span><i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $assignment->approval->inquiry->inquiry_Category)) }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-clock mr-1"></i>Pending Review
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold bg-blue-100 text-blue-800 rounded-full">
                        ID: {{ $assignment->approval->inquiry->inquiry_ID }}
                    </span>
                </div>
            </div>

            <!-- Inquiry Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Inquiry Description</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 leading-relaxed">{{ $assignment->approval->inquiry->inquiry_Description }}</p>
                    </div>

                    @if($assignment->approval->inquiry->inquiry_Supporting_Documents)
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Supporting Documents</h4>
                            <div class="flex items-center space-x-2 text-sm text-blue-600">
                                <i class="fas fa-paperclip"></i>
                                <a href="{{ route('inquiry.attachment', $assignment->approval->inquiry->inquiry_ID) }}" class="hover:underline">
                                    View Attachment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Assignment Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Assigned By:</span>
                            <span class="font-semibold">{{ $assignment->assignedByStaff->staff_Name ?? 'MCMC Staff' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Assignment Date:</span>
                            <span class="font-semibold">{{ $assignment->assignment_Date->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Priority:</span>
                            <span class="font-semibold text-orange-600">
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
                            <span class="text-gray-600">Expected Response:</span>
                            <span class="font-semibold">{{ $assignment->assignment_Date->addHours(48)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    @if($assignment->assignment_Comments)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2">MCMC Comments</h4>
                            <p class="text-blue-800 text-sm">{{ $assignment->assignment_Comments }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Jurisdiction Decision Form -->
        <div class="jurisdiction-card bg-white p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Jurisdiction Decision</h2>

            <!-- Decision Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Accept Jurisdiction -->
                <div class="jurisdiction-option jurisdiction-accept" onclick="selectJurisdiction('accept')">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Accept Jurisdiction</h3>
                            <p class="text-gray-600 text-sm mt-1">This inquiry falls within my agency's jurisdiction and I will proceed with the verification process.</p>
                            <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Inquiry is relevant to our agency</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>We have authority to investigate</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Resources are available</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reject Jurisdiction -->
                <div class="jurisdiction-option jurisdiction-reject" onclick="selectJurisdiction('reject')">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Reject Jurisdiction</h3>
                            <p class="text-gray-600 text-sm mt-1">This inquiry is outside my agency's jurisdiction and should be handled by another agency.</p>
                            <ul class="mt-3 text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-times text-red-500 mr-2"></i>Not within our authority</li>
                                <li><i class="fas fa-times text-red-500 mr-2"></i>Requires different expertise</li>
                                <li><i class="fas fa-times text-red-500 mr-2"></i>Should be reassigned</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept Form -->
            <div id="acceptForm" class="hidden">
                <form method="POST" action="{{ route('agency.assignments.accept', $assignment->assignment_ID) }}">
                    @csrf
                    <input type="hidden" name="jurisdiction_confirmation" value="1">

                    <div class="jurisdiction-warning p-4 rounded-lg mb-6">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-900">Jurisdiction Confirmation Required</h4>
                                <p class="text-yellow-800 text-sm mt-1">
                                    By accepting this assignment, you confirm that this inquiry falls within your agency's jurisdiction
                                    and you have the authority and resources to conduct the verification process.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="accept_comments" class="block text-sm font-bold mb-2">Comments (Optional)</label>
                        <textarea id="accept_comments" name="comments" rows="3"
                                  class="form-input w-full"
                                  placeholder="Add any comments about accepting this assignment..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="clearSelection()" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-2"></i>Accept & Start Review
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reject Form -->
            <div id="rejectForm" class="hidden">
                <form method="POST" action="{{ route('agency.assignments.reject', $assignment->assignment_ID) }}">
                    @csrf

                    <div class="jurisdiction-warning p-4 rounded-lg mb-6">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900">Rejection Information</h4>
                                <p class="text-blue-800 text-sm mt-1">
                                    Please provide a detailed reason for rejecting this assignment. This will help MCMC
                                    reassign the inquiry to the appropriate agency.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="jurisdiction_reason" class="block text-sm font-bold mb-2">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea id="jurisdiction_reason" name="jurisdiction_reason" rows="4"
                                  class="form-input w-full"
                                  placeholder="Please explain why this inquiry is outside your jurisdiction..."
                                  required></textarea>
                        <p class="text-sm text-gray-600 mt-1">
                            Be specific about why this inquiry doesn't fall under your agency's authority.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="suggested_agency" class="block text-sm font-bold mb-2">
                            Suggested Agency (Optional)
                        </label>
                        <select id="suggested_agency" name="suggested_agency" class="form-select w-full">
                            <option value="">Select an agency if you have a suggestion...</option>
                            @foreach(\App\Models\Agency::where('agency_ID', '!=', $assignment->agency_ID)->orderBy('agency_Name')->get() as $agency)
                                <option value="{{ $agency->agency_ID }}">{{ $agency->agency_Name }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-600 mt-1">
                            If you know which agency should handle this inquiry, please suggest it.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="clearSelection()" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban mr-2"></i>Reject Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectJurisdiction(decision) {
    // Clear previous selections
    document.querySelectorAll('.jurisdiction-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Hide all forms
    document.getElementById('acceptForm').classList.add('hidden');
    document.getElementById('rejectForm').classList.add('hidden');

    // Select current option and show corresponding form
    if (decision === 'accept') {
        document.querySelector('.jurisdiction-accept').classList.add('selected');
        document.getElementById('acceptForm').classList.remove('hidden');
    } else if (decision === 'reject') {
        document.querySelector('.jurisdiction-reject').classList.add('selected');
        document.getElementById('rejectForm').classList.remove('hidden');
    }
}

function clearSelection() {
    document.querySelectorAll('.jurisdiction-option').forEach(option => {
        option.classList.remove('selected');
    });
    document.getElementById('acceptForm').classList.add('hidden');
    document.getElementById('rejectForm').classList.add('hidden');
}

// Auto-resize textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
@endpush
