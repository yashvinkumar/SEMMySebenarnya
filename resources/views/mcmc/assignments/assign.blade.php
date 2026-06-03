@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Assign Inquiry to Agency - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link">
        <i class="fas fa-clipboard-list mr-2" aria-hidden="true"></i>
        Inquiries
    </a>
    <a href="{{ route('mcmc.assignments.list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-share-alt mr-2" aria-hidden="true"></i>
        Assignments
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
    <a href="{{ route('mcmc.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('mcmc.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
<div class="flex justify-center items-start py-8" style="color:#000 !important;">
    <div class="w-full max-w-4xl">
        <div class="card stats-card bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100 shadow-lg p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 pt-6 pb-2">
                <h4 class="text-lg font-semibold text-white mb-0 flex items-center">
                    <i class="fas fa-share-alt mr-2 text-white"></i>Assign Inquiry to Agency
                </h4>
            </div>
            <div class="card-body p-6 bg-white rounded-b-xl">
                <!-- Inquiry Details Section -->
                <div class="mb-6">
                    <div class="mb-4 font-bold text-lg" style="color:#000 !important;">Inquiry Details</div>
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Subject:</span>
                                <span style="color:#6b7280;">{{ $inquiry->inquiry_Title }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">User:</span>
                                <span style="color:#6b7280;">{{ $inquiry->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Type:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Category)) }}</span>
                            </div>
                            <div>
                                <span class="font-bold" style="color:#000 !important;">Status:</span>
                                <span style="color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}</span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="font-bold" style="color:#000 !important;">Description:</span>
                                <p style="color:#6b7280;">{{ $inquiry->inquiry_Description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($existingAssignment)
                    <!-- Existing Assignment Alert -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="font-semibold text-yellow-800">This inquiry is already assigned</span>
                        </div>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Assigned to: <strong>{{ $existingAssignment->agency->agency_Name }}</strong></p>
                            <p>Status: <strong>{{ $existingAssignment->formatted_status }}</strong></p>
                            <p>Assigned on: {{ $existingAssignment->assignment_Date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Assignment Form -->
                <form method="POST" action="{{ route('mcmc.assignments.assign', $inquiry->inquiry_ID) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="agency_id" class="block text-base font-bold mb-2" style="color:#000 !important;">
                            Select Agency <span class="text-danger">*</span>
                        </label>
                        <select id="agency_id" name="agency_id" class="form-select w-full text-black" style="color:#000 !important;" required>
                            <option value="">Select Agency</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->agency_ID }}" {{ old('agency_id') == $agency->agency_ID ? 'selected' : '' }}>
                                    {{ $agency->agency_Name }} ({{ $agency->formatted_agency_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('agency_id')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="comments" class="block text-base font-bold mb-2" style="color:#000 !important;">
                            Assignment Comments
                        </label>
                        <textarea id="comments" name="comments" rows="4" class="form-input w-full text-black" style="color:#000 !important;"
                                  placeholder="Add any comments or instructions for the agency (optional)">{{ old('comments') }}</textarea>
                        @error('comments')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn btn-primary px-6 py-2 text-white">
                            <i class="fas fa-share-alt mr-2"></i>Assign to Agency
                        </button>
                        <a href="{{ route('mcmc.inquiries.list') }}" class="btn btn-secondary px-6 py-2 text-white">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Inquiries
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for assignment
    const form = document.querySelector('form');
    const agencySelect = document.getElementById('agency_id');

    form.addEventListener('submit', function(e) {
        const selectedAgency = agencySelect.options[agencySelect.selectedIndex].text;
        if (!confirm(`Are you sure you want to assign this inquiry to ${selectedAgency.split(' (')[0]}?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endpush
