@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Edit Inquiry Submission - MySebenarnya
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
                    <i class="fas fa-edit mr-2 text-white"></i>Edit Inquiry Submission
                </h4>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <form method="POST" action="{{ route('inquiry.update', $inquiry->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Inquiry Type -->
                    <div class="mb-4">
                        <label for="inquiry_type" class="block text-base font-bold mb-2" style="color:#000 !important;">Inquiry Type <span class="text-danger">*</span></label>
                        <select id="inquiry_Category" name="inquiry_Category" class="form-select w-full text-black" style="color:#000 !important;" required>
                            <option value="">Select Inquiry Type</option>
                            <option value="technical" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="general" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="broadcasting" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'broadcasting' ? 'selected' : '' }}>Broadcasting</option>
                            <option value="telecommunications" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                            <option value="internet" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'internet' ? 'selected' : '' }}>Internet</option>
                            <option value="complaint" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="suggestion" {{ (old('inquiry_Category') ?? $inquiry->inquiry_Category) == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                        </select>
                        @error('inquiry_Category')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="inquiry_Title" class="block text-base font-bold mb-2" style="color:#000 !important;">Subject <span class="text-danger">*</span></label>
                        <input id="inquiry_Title" type="text" name="inquiry_Title" class="form-input w-full text-black" style="color:#000 !important;" value="{{ old('inquiry_Title') ?? $inquiry->inquiry_Title }}" required maxlength="255">
                        @error('inquiry_Title')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="inquiry_Description" class="block text-base font-bold mb-2" style="color:#000 !important;">Description <span class="text-danger">*</span></label>
                        <textarea id="inquiry_Description" name="inquiry_Description" rows="6" class="form-input w-full text-black" style="color:#000 !important;" required>{{ old('inquiry_Description') ?? $inquiry->inquiry_Description }}</textarea>
                        @error('inquiry_Description')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Current Attachment -->
                    @if ($inquiry->inquiry_Attachment_URL)
                        <div class="mb-4">
                            <label class="block text-base font-bold mb-2" style="color:#000 !important;">Current Attachment</label>
                            <div class="flex items-center">
                                <a href="{{ asset('storage/' . $inquiry->inquiry_Attachment_URL) }}" target="_blank" class="btn btn-outline-primary btn-sm mr-2 text-black" style="color:#000 !important;">
                                    <i class="fas fa-download"></i> View Current File
                                </a>
                                <small class="text-gray-500">{{ basename($inquiry->inquiry_Attachment_URL) }}</small>
                            </div>
                        </div>
                    @endif

                    <!-- New Attachment -->
                    <div class="mb-4">
                        <label for="attachment" class="block text-base font-bold mb-2" style="color:#000 !important;">New Attachment</label>
                        <input id="attachment" type="file" name="attachment" class="form-input w-full text-black" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                        <small class="text-gray-500">Leave empty to keep current attachment. Allowed file types: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT (Max: 5MB)</small>
                        @error('attachment')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status (Read-only) -->
                    <div class="mb-4">
                        <label class="block text-base font-bold mb-2" style="color:#000 !important;">Status</label>
                        <input type="text" class="form-input w-full text-gray-500 bg-gray-100" style="color:#6b7280 !important;" value="{{ ucfirst($inquiry->status) }}" readonly>
                        <small class="text-gray-500">Status can only be updated by MCMC staff</small>
                    </div>

                    <!-- Submission Date (Read-only) -->
                    <div class="mb-4">
                        <label class="block text-base font-bold mb-2" style="color:#000 !important;">Submitted On</label>
                        <input type="text" class="form-input w-full text-gray-500 bg-gray-100" style="color:#6b7280 !important;" value="{{ $inquiry->created_at->format('d/m/Y H:i') }}" readonly>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3 mt-6">
                        @php
                            $status = strtolower($inquiry->status ?? $inquiry->inquiry_Status ?? '');
                        @endphp
                        @if ($status == 'pending' || $status == 'in_progress')
                            <button type="submit" class="btn btn-primary px-6 py-2 text-white">Update Inquiry</button>
                        @else
                            <div class="alert alert-info">This inquiry cannot be edited as it has been {{ $status }}.</div>
                        @endif
                        <a href="{{ route('inquiry.history') }}" class="btn btn-secondary px-6 py-2 text-white">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
