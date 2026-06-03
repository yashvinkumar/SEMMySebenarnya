@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Delete Inquiry Submission - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('public.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('inquiry.history') }}" class="nav-link active text-primary fw-semibold">
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
    <a href="{{ route('public.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('public.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
<div class="flex justify-center items-start py-8" style="color:#000 !important;">
    <div class="w-full max-w-2xl">
        <div class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 shadow-lg p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
                <h4 class="text-lg font-semibold text-white mb-0 flex items-center">
                    <i class="fas fa-trash mr-2 text-white"></i>Delete Inquiry Submission
                </h4>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <div class="alert alert-danger text-black font-bold mb-0" style="margin-bottom:0;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Warning! This action cannot be undone. Are you sure you want to delete this inquiry?
                </div>
                <!-- Inquiry Details -->
                <div class="mb-6" style="margin-top:-32px;">
                    <div class="mb-4 font-bold" style="color:#000 !important; margin-top:0;">Inquiry Details</div>
                    <div class="pl-8">
                    <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Subject:</span> <span style="color:#6b7280;">{{ $inquiry->inquiry_Title }}</span></div>
                    <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Type:</span> <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}</span></div>
                    <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Status:</span> <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}</span></div>
                    <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Submitted On:</span> <span style="color:#6b7280;">{{ $inquiry->inquiry_Created_At ? \Carbon\Carbon::parse($inquiry->inquiry_Created_At)->format('d/m/Y H:i') : 'N/A' }}</span></div>
                    <div class="mb-2"><span class="font-bold" style="color:#000 !important;">Description:</span> <span style="color:#6b7280;">{{ $inquiry->inquiry_Description }}</span></div>
                    @if ($inquiry->inquiry_Attachment_URL)
                        <div class="mb-2">
                            <span class="font-bold" style="color:#000 !important;">Attachment:</span>
                            <a href="{{ route('inquiry.attachment', $inquiry->id) }}" target="_blank" class="btn btn-outline-primary btn-sm ml-2 text-black" style="color:#000 !important;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    @endif
                    </div>
                </div>
                <!-- Deletion Restrictions -->
                @if($inquiry->inquiry_Status == 'completed' || $inquiry->inquiry_Status == 'in_progress')
                    <div class="alert alert-info text-black mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> This inquiry is currently {{ str_replace('_', ' ', $inquiry->inquiry_Status) }}. You may want to contact MCMC staff before deleting.
                    </div>
                @endif
                @if($inquiry->inquiry_Status == 'rejected')
                    <div class="alert alert-secondary text-black mb-4">
                        <i class="fas fa-info-circle"></i>
                        This inquiry has been rejected and can be safely deleted.
                    </div>
                @endif
                <!-- Delete Form -->
                <form method="POST" action="{{ route('inquiry.destroy', $inquiry->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="confirmDelete" name="confirmDelete" required class="mr-2">
                        <label for="confirmDelete" class="mb-0" style="color:#000 !important;">I understand that this action cannot be undone and I want to permanently delete this inquiry.</label>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('inquiry.history') }}" class="btn btn-secondary px-6 py-2 text-white">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger px-6 py-2 text-white">
                            <i class="fas fa-trash"></i> Delete Inquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
