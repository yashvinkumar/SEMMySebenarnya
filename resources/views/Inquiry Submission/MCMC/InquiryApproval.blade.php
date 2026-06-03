@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Review & Approve Inquiry - MySebenarnya
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
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2" aria-hidden="true"></i>
        Manage Users
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
    <div class="flex justify-center items-start py-8" style="color:#000 !important;">
        <div class="w-full max-w-2xl">
            <div
                class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 shadow-lg p-0 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
                    <h4 class="text-lg font-semibold text-white mb-0 flex items-center">
                        <i class="fas fa-cogs mr-2 text-white"></i>Review & Approve Inquiry
                    </h4>
                </div>
                <div class="card-body p-6 bg-white rounded-b-xl">
                    <div class="mb-4 font-bold" style="color:#000 !important;">Inquiry Details</div>
                    <div class="pl-8 mb-6">
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Subject:</span> <span
                                style="color:#6b7280;">{{ $inquiry->inquiry_Title }}</span></div>
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">User:</span> <span
                                style="color:#6b7280;">{{ $inquiry->user->name ?? 'N/A' }}
                                ({{ $inquiry->user->email ?? 'N/A' }})</span></div>
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Type:</span> <span
                                style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}</span>
                        </div>
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Status:</span> <span
                                style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}</span>
                        </div>
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Submitted On:</span>
                            <span
                                style="color:#6b7280;">{{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Description:</span> <span
                                style="color:#6b7280;">{{ $inquiry->inquiry_Description }}</span></div>
                        @if ($inquiry->inquiry_Attachment_URL)
                            <div class="mb-2">
                                <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                                <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm ml-2 text-black" style="color:#000 !important;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>

                    @php
                        $currentAssignment = $inquiry->currentAssignment();
                        $assignedAgency = $inquiry->assignedAgency();
                    @endphp

                    @if ($currentAssignment && $assignedAgency)
                        <div class="mb-4 font-bold" style="color:#000 !important;">Agency Assignment</div>
                        <div class="pl-8 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="mb-2">
                                    <span class="font-bold" style="color:#000 !important;">Assigned Agency:</span>
                                    <span style="color:#6b7280;">{{ $assignedAgency->agency_Name }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold" style="color:#000 !important;">Agency Type:</span>
                                    <span style="color:#6b7280;">{{ $assignedAgency->formatted_agency_type }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold" style="color:#000 !important;">Assignment Date:</span>
                                    <span
                                        style="color:#6b7280;">{{ $currentAssignment->assignment_Date->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold" style="color:#000 !important;">Assignment Status:</span>
                                    @php
                                        $statusColors = [
                                            'pending' => 'text-yellow-600',
                                            'in_progress' => 'text-blue-600',
                                            'completed' => 'text-green-600',
                                            'rejected' => 'text-red-600',
                                        ];
                                    @endphp
                                    <span
                                        class="{{ $statusColors[$currentAssignment->assignment_Status] ?? 'text-gray-600' }}">
                                        {{ $currentAssignment->formatted_status }}
                                    </span>
                                </div>
                                @if ($currentAssignment->assignment_Comments)
                                    <div class="mb-2">
                                        <span class="font-bold" style="color:#000 !important;">Agency Comments:</span>
                                        <p style="color:#6b7280;" class="mt-1">
                                            {{ $currentAssignment->assignment_Comments }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif



                    <form method="POST" action="{{ route('mcmc.inquiries.approve.submit', $inquiry->inquiry_ID) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="status" class="block text-base font-bold mb-2"
                                style="color:#000 !important;">Update Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select w-full text-black"
                                style="color:#000 !important;" required>
                                <option value="">Select Status</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                                <option value="under_review">Under Review</option>
                                <option value="assign_to_agency">Assigned to Agency</option>
                            </select>
                            @error('status')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="comments" class="block text-base font-bold mb-2"
                                style="color:#000 !important;">Comments</label>
                            <textarea id="comments" name="comments" rows="4" class="form-input w-full text-black"
                                style="color:#000 !important;" placeholder="Add any comments for the user (optional)"></textarea>
                            @error('comments')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="submit" class="btn btn-primary px-6 py-2 text-white">Submit</button>
                            <a href="{{ route('mcmc.inquiries.list') }}" class="btn btn-secondary px-6 py-2 text-white">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
