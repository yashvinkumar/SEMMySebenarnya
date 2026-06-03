@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Submit New Inquiry - MySebenarnya
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
                    <i class="fas fa-plus-circle mr-2 text-white"></i>Submit New Inquiry
                </h4>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <form method="POST" action="{{ route('inquiry.store') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- Inquiry Type -->
                    <div class="mb-4">
                        <label for="inquiry_Category" class="block text-base font-bold mb-2" style="color:#000 !important;">Inquiry Type <span class="text-danger">*</span></label>
                        <select id="inquiry_Category" name="inquiry_Category" class="form-select w-full text-black" style="color:#000 !important;" required>
                            <option value="">Select Inquiry Type</option>
                            <option value="technical" {{ old('inquiry_Category') == 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="general" {{ old('inquiry_Category') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="broadcasting" {{ old('inquiry_Category') == 'broadcasting' ? 'selected' : '' }}>Broadcasting</option>
                            <option value="telecommunications" {{ old('inquiry_Category') == 'telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                            <option value="internet" {{ old('inquiry_Category') == 'internet' ? 'selected' : '' }}>Internet</option>
                            <option value="complaint" {{ old('inquiry_Category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="suggestion" {{ old('inquiry_Category') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                        </select>
                        @error('inquiry_Category')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="inquiry_Title" class="block text-base font-bold mb-2" style="color:#000 !important;">Subject <span class="text-danger">*</span></label>
                        <input id="inquiry_Title" type="text" name="inquiry_Title" class="form-input w-full text-black" style="color:#000 !important;" value="{{ old('inquiry_Title') }}" required maxlength="255">
                        @error('inquiry_Title')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Description -->
                    <div class="mb-4">
                        <label for="inquiry_Description" class="block text-base font-bold mb-2" style="color:#000 !important;">Description <span class="text-danger">*</span></label>
                        <textarea id="inquiry_Description" name="inquiry_Description" rows="6" class="form-input w-full text-black" style="color:#000 !important;" required>{{ old('inquiry_Description') }}</textarea>
                        @error('inquiry_Description')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Attachment -->
                    <div class="mb-4">
                        <label for="attachment" class="block text-base font-bold mb-2" style="color:#000 !important;">Attachment</label>
                        <input id="attachment" type="file" name="attachment" class="form-input w-full text-black" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" placeholder="Upload your file" style="color:#000 !important;">
                        <script>
                        // For browsers that do not show placeholder for file input, show a label overlay
                        document.addEventListener('DOMContentLoaded', function() {
                            var fileInput = document.getElementById('attachment');
                            if (fileInput) {
                                fileInput.addEventListener('change', function() {
                                    fileInput.classList.remove('text-gray-400');
                                });
                                if (!fileInput.value) {
                                    fileInput.classList.add('text-gray-400');
                                }
                            }
                        });
                        </script>
                        <small class="text-gray-500">Optional. Allowed file types: PDF, DOC, DOCX, JPG, JPEG, PNG, TXT (Max: 5MB)</small>
                        @error('attachment')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Submit Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn btn-primary px-6 py-2 text-white">Submit Inquiry</button>
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
