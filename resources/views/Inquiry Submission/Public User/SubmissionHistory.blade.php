@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    My Inquiries - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('public.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('inquiry.history') }}" class="nav-link active">
        <i class="fas fa-clipboard-list mr-2" aria-hidden="true"></i>
        My Inquiries
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-file-alt mr-2" aria-hidden="true"></i>
        Services
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-history mr-2" aria-hidden="true"></i>
        History
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('public.profile') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('public.settings') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-8 border border-blue-100">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    @if (isset($viewAllPublic) && $viewAllPublic)
                        All Public Inquiries (Anonymous View)
                    @else
                        My Inquiry Submissions
                    @endif
                </h2>
                <p class="text-gray-600">
                    @if (isset($viewAllPublic) && $viewAllPublic)
                        Browse inquiries from all public users. Personal information is hidden for privacy.
                    @else
                        Track and manage all your submitted inquiries in one place.
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('inquiry.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                    New Inquiry
                </a>
            </div>
        </div>
    </div>





    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Inquiries</p>
                    <p class="text-3xl font-bold">{{ $totalCount }}</p>
                    <p class="text-blue-100 text-xs mt-1">
                        @if (isset($viewAllPublic) && $viewAllPublic)
                            All public submissions
                        @else
                            All submissions
                        @endif
                    </p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clipboard-list text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl p-6 shadow-lg"
            style="background: linear-gradient(to right, #eab308, #ca8a04) !important;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold">{{ $pendingCount }}</p>
                    <p class="text-yellow-100 text-xs mt-1">Under review</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clock text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">In Progress</p>
                    <p class="text-3xl font-bold">{{ $inProgressCount }}</p>
                    <p class="text-blue-100 text-xs mt-1">Being processed</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-spinner text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold">{{ $completedCount }}</p>
                    <p class="text-green-100 text-xs mt-1">Finished</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Rejected</p>
                    <p class="text-3xl font-bold">{{ $rejectedCount }}</p>
                    <p class="text-red-100 text-xs mt-1">Need revision</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card stats-card mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
            <h3 class="text-lg font-semibold text-white">Filter Inquiries</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inquiry.history') }}" class="flex flex-col gap-4">

                <!-- View Scope Filter (only for public/agency users) -->
                @if (auth()->user() && (auth()->user()->user_type === 'public' || auth()->user()->user_type === 'agency'))
                    <div class="flex-1 min-w-0">
                        <label for="view_scope" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">View Scope</label>
                        <select id="view_scope" name="view_scope" class="form-select text-black w-full"
                            style="color:#000 !important;">
                            <option value="my_inquiries"
                                {{ request('view_scope', 'my_inquiries') == 'my_inquiries' ? 'selected' : '' }}>My
                                Inquiries Only</option>
                            <option value="all_public" {{ request('view_scope') == 'all_public' ? 'selected' : '' }}>All
                                Public Inquiries (Anonymous)</option>
                        </select>
                        <small class="text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            "All Public Inquiries" shows inquiries from all users without personal information
                        </small>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="search" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">Search</label>
                        <input id="search" type="text" name="search" class="form-input text-black w-full"
                            placeholder="Search inquiries..." value="{{ request('search') }}"
                            style="color:#000 !important;">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="status" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">Status</label>
                        <select id="status" name="status" class="form-select text-black w-full"
                            style="color:#000 !important;">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                Progress
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review
                            </option>
                            <option value="assign_to_agency" {{ request('status') == 'assign_to_agency' ? 'selected' : '' }}>Assigned to Agency
                            </option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="category" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">Category</label>
                        <select id="category" name="category" class="form-select text-black w-full"
                            style="color:#000 !important;">
                            <option value="">All Categories</option>
                            <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="broadcasting" {{ request('category') == 'broadcasting' ? 'selected' : '' }}>Broadcasting</option>
                            <option value="telecommunications" {{ request('category') == 'telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                            <option value="internet" {{ request('category') == 'internet' ? 'selected' : '' }}>Internet</option>
                            <option value="complaint" {{ request('category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="suggestion" {{ request('category') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary btn-sm px-3 py-1 text-black"
                            style="height:32px;min-height:0;">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                        <a href="{{ route('inquiry.history') }}"
                            class="btn btn-secondary btn-sm px-3 py-1 text-black ml-2" style="height:32px;min-height:0;">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inquiries List -->
    @if ($inquiries->count() > 0)
        <div class="grid grid-cols-1 gap-6 mb-8">
            @foreach ($inquiries as $inquiry)
                <div class="card stats-card hover:shadow-lg transition-shadow duration-200">
                    <div class="card-body">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        #{{ $inquiry->id }}
                                    </span>
                                    @if (isset($showPersonalInfo) && !$showPersonalInfo)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-user-secret mr-1"></i>Anonymous
                                        </span>
                                    @endif
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if (strtolower($inquiry->inquiry_Status) == 'completed') bg-green-100 text-green-800
                                        @elseif(strtolower($inquiry->inquiry_Status) == 'in_progress') bg-blue-100 text-blue-800
                                        @elseif(strtolower($inquiry->inquiry_Status) == 'rejected') bg-red-100 text-red-800
                                        @elseif(strtolower($inquiry->inquiry_Status) == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $inquiry->inquiry_Title }}
                                </h3>

                                <p class="text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit($inquiry->inquiry_Description, 150) }}
                                </p>

                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('M d, Y') : 'N/A' }}
                                    </div>
                                    @if ($inquiry->inquiry_Attachment_URL)
                                        <div class="flex items-center">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            Has attachment
                                        </div>
                                    @endif
                                </div>

                                {{-- Agency Assignment Information --}}
                                @php
                                    $agencyInfo = $inquiry->getAgencyAssignmentInfo();
                                @endphp
                                @if ($agencyInfo)
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center space-x-2 text-sm">
                                            <i class="fas fa-building text-blue-600"></i>
                                            <span class="font-medium text-blue-800">Assigned to Agency:</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-700">
                                            <div class="font-semibold text-blue-700">{{ $agencyInfo['agency_name'] }}
                                            </div>
                                            <div class="flex items-center space-x-4 mt-1 text-xs text-gray-600">
                                                <span>
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    Assigned:
                                                    {{ \Carbon\Carbon::parse($agencyInfo['assigned_date'])->format('M d, Y') }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if ($agencyInfo['assignment_status'] == 'completed') bg-green-100 text-green-800
                                                    @elseif($agencyInfo['assignment_status'] == 'in_progress') bg-blue-100 text-blue-800
                                                    @elseif($agencyInfo['assignment_status'] == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $agencyInfo['assignment_status'])) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2 ml-4">
                                <button type="button" class="btn btn-sm btn-primary text-white" title="View Details"
                                    data-modal-target="#inquiryModal{{ $inquiry->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if (isset($showPersonalInfo) && $showPersonalInfo)
                                    @if (strtolower($inquiry->inquiry_Status) == 'pending')
                                        <a href="{{ route('inquiry.edit', $inquiry->id) }}"
                                            class="btn btn-sm btn-secondary text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if ($inquiry->inquiry_Attachment_URL)
                                        <a href="{{ route('inquiry.attachment', $inquiry->id) }}"
                                            class="btn btn-sm btn-success text-white" title="Download Attachment">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif

                                    @if (strtolower($inquiry->inquiry_Status) == 'pending')
                                        <a href="{{ route('inquiry.delete', $inquiry->id) }}"
                                            class="btn btn-sm btn-danger text-white" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this inquiry?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @endif
                                @else
                                    @if ($inquiry->inquiry_Attachment_URL)
                                        <span class="btn btn-sm btn-outline-secondary text-gray-500"
                                            title="Attachment Available (Anonymous View)">
                                            <i class="fas fa-paperclip"></i>
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inquiry Detail Modal -->
                <div id="inquiryModal{{ $inquiry->id }}"
                    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
                    style="background:rgba(255,255,255,0.96);">
                    <div class="card stats-card rounded-xl w-full mx-auto p-0 overflow-hidden flex flex-col items-center justify-center relative"
                        style="max-width: 340px !important; min-width: 0;">
                        <div
                            class="bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-between px-4 pt-6 pb-2 w-full">
                            <div class="flex items-center">
                                <div class="bg-blue-400 text-white rounded-full p-2 mr-3">
                                    <i class="fas fa-info-circle text-lg text-white" aria-hidden="true"></i>
                                </div>
                                <h4 class="text-lg font-semibold mb-0 text-white">Inquiry Details</h4>
                            </div>
                            <button style="color:#fff !important;" class="hover:text-gray-200 focus:outline-none"
                                onclick="closeModal('inquiryModal{{ $inquiry->id }}')">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="card-body p-4 bg-white rounded-b-xl w-full" style="color:#000 !important;">
                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Subject:</span>
                                <span style="color:#6b7280;">{{ $inquiry->inquiry_Title }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Category:</span>
                                <span
                                    style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Status:</span>
                                <span
                                    style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Submitted On:</span>
                                <span
                                    style="color:#6b7280;">{{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>

                            {{-- Agency Assignment Information in Modal --}}
                            @php
                                $agencyInfo = $inquiry->getAgencyAssignmentInfo();
                            @endphp
                            @if ($agencyInfo)
                                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="mb-2">
                                        <span class="font-bold text-blue-800" style="color:#1e40af !important;">
                                            <i class="fas fa-building mr-1"></i>Assigned to Agency:
                                        </span>
                                    </div>
                                    <div class="ml-5">
                                        <div class="font-semibold text-blue-700 mb-1">{{ $agencyInfo['agency_name'] }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="mb-1">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <span class="font-medium">Assigned Date:</span>
                                                {{ \Carbon\Carbon::parse($agencyInfo['assigned_date'])->format('M d, Y') }}
                                            </div>
                                            <div>
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <span class="font-medium">Status:</span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-1
                                                    @if ($agencyInfo['assignment_status'] == 'completed') bg-green-100 text-green-800
                                                    @elseif($agencyInfo['assignment_status'] == 'in_progress') bg-blue-100 text-blue-800
                                                    @elseif($agencyInfo['assignment_status'] == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $agencyInfo['assignment_status'])) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Description:</span>
                                <span style="color:#6b7280;">{{ $inquiry->inquiry_Description }}</span>
                            </div>
                            @if ($inquiry->inquiry_Attachment_URL)
                                <div class="mb-4">
                                    <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                                    @if (isset($showPersonalInfo) && $showPersonalInfo)
                                        <a href="{{ route('inquiry.attachment', $inquiry->id) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm ml-2 text-black"
                                            style="color:#000 !important;">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-gray-500 ml-2">
                                            <i class="fas fa-paperclip mr-1"></i>Available (Anonymous View)
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <div class="mt-6 flex justify-end items-center gap-2">
                                <button class="btn btn-secondary" style="color:#000 !important;"
                                    onclick="closeModal('inquiryModal{{ $inquiry->id }}')">
                                    <i class="fas fa-arrow-left mr-2"></i>Close
                                </button>
                                @if (isset($showPersonalInfo) && $showPersonalInfo)
                                    @if (strtolower($inquiry->inquiry_Status) == 'pending')
                                        <a href="{{ route('inquiry.edit', $inquiry->id) }}"
                                            class="btn btn-outline-primary ml-2" style="color:#000 !important;">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($inquiries->hasPages())
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }}
                    of {{ $inquiries->total() }} results
                </div>
                <div>
                    {{ $inquiries->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="card stats-card">
            <div class="card-body text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No inquiries found</h3>
                <p class="text-gray-600 mb-6">
                    @if (request()->hasAny(['search', 'status', 'category']))
                        No inquiries match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't submitted any inquiries yet. Get started by creating your first inquiry.
                    @endif
                </p>
                <div class="flex items-center justify-center space-x-4">
                    <a href="{{ route('inquiry.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Inquiry
                    </a>
                    @if (request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('inquiry.history') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile Create Button -->
    <div class="md:hidden fixed bottom-6 right-6">
        <a href="{{ route('inquiry.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-colors duration-200">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>

    <!-- Modal Script -->
    <style>
        /* Ensure modal buttons and text are always visible on light backgrounds */
        .modal-override-btn,
        .modal-override-btn.btn,
        .modal-override-btn.btn-secondary,
        .modal-override-btn.btn-outline-primary {
            color: #1a202c !important;
            /* text-gray-900 */
            border-color: #6366f1 !important;
            /* indigo-500 for outline */
            background-color: #fff !important;
        }

        .modal-override-btn.btn-secondary {
            background-color: #f3f4f6 !important;
            /* gray-100 */
        }

        .modal-override-btn.btn-outline-primary:hover {
            background-color: #6366f1 !important;
            color: #fff !important;
        }

        .modal-override-label {
            color: #1a202c !important;
        }
    </style>
    <script>
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        document.querySelectorAll('[data-modal-target]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var modalId = btn.getAttribute('data-modal-target').replace('#', '');
                document.getElementById(modalId).classList.remove('hidden');
            });
        });

        // Auto-submit form when view scope changes
        document.addEventListener('DOMContentLoaded', function() {
            const viewScopeSelect = document.getElementById('view_scope');
            if (viewScopeSelect) {
                viewScopeSelect.addEventListener('change', function() {
                    // Clear other filters when changing view scope to avoid confusion
                    document.getElementById('search').value = '';
                    document.getElementById('status').value = '';
                    document.getElementById('category').value = '';

                    // Submit the form
                    this.form.submit();
                });
            }
        });
    </script>
@endsection
