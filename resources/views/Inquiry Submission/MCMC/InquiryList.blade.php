@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    MCMC Inquiry Management - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-clipboard-list mr-2" aria-hidden="true"></i>
        Inquiries
    </a>
    <a href="{{ route('mcmc.inquiries.assign-page') }}" class="nav-link">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        Assign Inquiries
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2" aria-hidden="true"></i>
        Manage Users
    </a>
    <a href="{{ route('mcmc.register.agency') }}" class="nav-link">
        <i class="fas fa-building mr-2" aria-hidden="true"></i>
        Register Agency
    </a>
    <a href="{{ route('mcmc.reports.index') }}" class="nav-link">
        <i class="fas fa-chart-bar mr-2" aria-hidden="true"></i>
        Reports
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('mcmc.profile') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('mcmc.settings') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-xl font-semibold text-gray-900">MCMC Inquiry Management</h2>
        <a href="{{ route('mcmc.inquiries.reports') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar mr-2"></i> Generate Report
        </a>
    </div>

    <!-- Filters -->
    <div class="card stats-card mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
            <h3 class="text-lg font-semibold text-white">Filter Inquiries</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('mcmc.inquiries.list') }}">
                <!-- First Row -->
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4 mb-4">
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
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="assign_to_agency" {{ request('status') == 'assign_to_agency' ? 'selected' : '' }}>Assigned to Agency</option>
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
                </div>

                <!-- Second Row -->
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="start_date" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">Start Date</label>
                        <input id="start_date" type="date" name="start_date" class="form-input text-black w-full"
                            value="{{ request('start_date') }}" style="color:#000 !important;">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="end_date" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">End Date</label>
                        <input id="end_date" type="date" name="end_date" class="form-input text-black w-full"
                            value="{{ request('end_date') }}" style="color:#000 !important;">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="agency" class="block text-base font-bold text-black mb-2"
                            style="color:#000 !important;">Assigned Agency</label>
                        <select id="agency" name="agency" class="form-select text-black w-full"
                            style="color:#000 !important;">
                            <option value="">All Agencies</option>
                            @if (isset($agencies))
                                @foreach ($agencies as $agency)
                                    <option value="{{ $agency->agency_ID }}"
                                        {{ request('agency') == $agency->agency_ID ? 'selected' : '' }}>
                                        {{ $agency->agency_Name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary btn-sm px-3 py-1 text-black"
                            style="height:32px;min-height:0;">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                        <a href="{{ route('mcmc.inquiries.list') }}"
                            class="btn btn-secondary btn-sm px-3 py-1 text-black ml-2" style="height:32px;min-height:0;">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                                        #{{ $inquiry->inquiry_ID }}
                                    </span>
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        switch(strtolower($inquiry->inquiry_Status)) {
                                            case 'completed':
                                                $statusClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'in_progress':
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'bg-red-100 text-red-800';
                                                break;
                                            case 'pending':
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'under_review':
                                                $statusClass = 'bg-orange-100 text-orange-800';
                                                break;
                                            case 'assign_to_agency':
                                                $statusClass = 'bg-teal-100 text-teal-800';
                                                break;
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $inquiry->inquiry_Title }}
                                </h3>
                                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                                    <div><i class="fas fa-user mr-1"></i> {{ $inquiry->user->name ?? 'N/A' }}</div>
                                    <div><i class="fas fa-envelope mr-1"></i> {{ $inquiry->user->email ?? 'N/A' }}</div>
                                </div>
                                <p class="text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit($inquiry->inquiry_Description, 150) }}
                                </p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('M d, Y H:i') : 'N/A' }}
                                    </div>
                                    @if ($inquiry->inquiry_Attachment_URL)
                                        <div class="flex items-center">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}"
                                                class="text-blue-600 hover:underline" target="_blank">Download
                                                Attachment</a>
                                        </div>
                                    @endif
                                    @php
                                        $assignedAgency = $inquiry->assignedAgency();
                                    @endphp
                                    @if ($assignedAgency)
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-1"></i>
                                            <span
                                                class="text-purple-600 font-medium">{{ $assignedAgency->agency_Name }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <span class="text-orange-600">Not Assigned</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-row items-end gap-3 ml-4">
                                @if (!in_array(strtolower($inquiry->inquiry_Status), ['completed', 'rejected']))
                                    <a href="{{ route('mcmc.inquiries.approve', $inquiry->inquiry_ID) }}"
                                        class="btn btn-warning btn-sm text-white">
                                        <i class="fas fa-edit mr-1"></i> Review
                                    </a>
                                @endif
                                <button type="button" class="btn btn-primary btn-sm text-white"
                                    data-modal-target="#viewModal{{ $inquiry->inquiry_ID }}">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal for Details -->
                <div id="viewModal{{ $inquiry->inquiry_ID }}"
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
                                onclick="document.getElementById('viewModal{{ $inquiry->inquiry_ID }}').classList.add('hidden');">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="card-body p-4 bg-white rounded-b-xl w-full" style="color:#000 !important;">
                            <div class="mb-4"><span class="font-bold" style="color:#000 !important;">Subject:</span>
                                <span style="color:#6b7280;">{{ $inquiry->inquiry_Title }}</span>
                            </div>
                            <div class="mb-4"><span class="font-bold" style="color:#000 !important;">User:</span> <span
                                    style="color:#6b7280;">{{ $inquiry->user->name ?? 'N/A' }}
                                    ({{ $inquiry->user->email ?? 'N/A' }})
                                </span></div>
                            <div class="mb-4"><span class="font-bold" style="color:#000 !important;">Category:</span>
                                <span
                                    style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}</span>
                            </div>
                            <div class="mb-4"><span class="font-bold" style="color:#000 !important;">Status:</span>
                                <span
                                    style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}</span>
                            </div>
                            <div class="mb-4"><span class="font-bold" style="color:#000 !important;">Submitted
                                    On:</span> <span
                                    style="color:#6b7280;">{{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div class="mb-4"><span class="font-bold"
                                    style="color:#000 !important;">Description:</span> <span
                                    style="color:#6b7280;">{{ $inquiry->inquiry_Description }}</span></div>
                            @if ($inquiry->inquiry_Attachment_URL)
                                <div class="mb-4">
                                    <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                                    <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm ml-2 text-black"
                                        style="color:#000 !important;">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            @endif
                            @php
                                $assignedAgency = $inquiry->assignedAgency();
                            @endphp
                            <div class="mb-4">
                                <span class="font-bold" style="color:#000 !important;">Assigned Agency:</span>
                                @if ($assignedAgency)
                                    <span style="color:#7c3aed;">{{ $assignedAgency->agency_Name }}</span>
                                @else
                                    <span style="color:#f59e0b;">Not Assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Modal Script for all modals -->
        <script>
            document.querySelectorAll('[data-modal-target]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var modalId = btn.getAttribute('data-modal-target').replace('#', '');
                    document.getElementById(modalId).classList.remove('hidden');
                });
            });
        </script>
        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of
                {{ $inquiries->total() }} results
            </div>
            <div>
                {{ $inquiries->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No inquiries found</h5>
            <p class="text-muted">There are no inquiries matching your criteria.</p>
        </div>
    @endif
@endsection
