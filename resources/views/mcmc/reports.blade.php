@extends('layouts.dashboard')

@section('title', 'Reports - MySebenarnya')

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2" aria-hidden="true"></i>
        Manage Users
    </a>
    <a href="{{ route('mcmc.register.agency') }}" class="nav-link">
        <i class="fas fa-building mr-2" aria-hidden="true"></i>
        Register Agency
    </a>
    <a href="{{ route('mcmc.reports.index') }}" class="nav-link active">
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

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-chart-bar mr-3 text-green-600" aria-hidden="true"></i>
                Reports & Analytics
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Generate comprehensive reports and view platform analytics
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('mcmc.users') }}"
               class="btn btn-secondary">
                <i class="fas fa-users mr-2" aria-hidden="true"></i>
                Manage Users
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mr-3 text-red-600 mt-0.5" aria-hidden="true"></i>
                <div>
                    <p class="font-medium mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Public Users</p>
                    <p class="text-3xl font-bold">{{ \App\Models\UserRecord::where('user_type', 'public')->count() }}</p>
                    <p class="text-blue-100 text-xs mt-1">Registered citizens</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-users text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Agencies</p>
                    <p class="text-3xl font-bold">{{ \App\Models\UserRecord::where('user_type', 'agency')->count() }}</p>
                    <p class="text-green-100 text-xs mt-1">Organizations</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-building text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">MCMC Staff</p>
                    <p class="text-3xl font-bold">{{ \App\Models\UserRecord::where('user_type', 'mcmc')->count() }}</p>
                    <p class="text-purple-100 text-xs mt-1">Staff members</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-user-tie text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Report Generation Form -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-alt mr-2 text-green-600" aria-hidden="true"></i>
                    Generate New Report
                </h2>
                <p class="text-sm text-gray-600 mt-1">Create and download comprehensive reports</p>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('mcmc.reports.generate') }}" class="space-y-6">
                    @csrf

                    <!-- Report Type Selection -->
                    <div>
                        <label for="report_type" class="form-label">
                            <i class="fas fa-list mr-2 text-gray-400" aria-hidden="true"></i>
                            Report Type
                        </label>
                        <select id="report_type"
                                name="report_type"
                                class="form-select @error('report_type') error @enderror"
                                required
                                aria-describedby="@error('report_type') report-type-error @enderror report-type-help">
                            <option value="">Select Report Type</option>
                            <option value="public_users" {{ old('report_type') == 'public_users' ? 'selected' : '' }}>
                                Public Users Report
                            </option>
                            <option value="agencies" {{ old('report_type') == 'agencies' ? 'selected' : '' }}>
                                Agencies Report
                            </option>
                            <option value="all" {{ old('report_type') == 'all' ? 'selected' : '' }}>
                                Complete Users Report
                            </option>
                        </select>
                        @error('report_type')
                            <div id="report-type-error" class="form-error" role="alert">
                                <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="report-type-help" class="form-help">
                            Choose the type of data to include in your report.
                        </div>
                    </div>

                    <!-- Export Format Selection -->
                    <div>
                        <label for="format" class="form-label">
                            <i class="fas fa-file-export mr-2 text-gray-400" aria-hidden="true"></i>
                            Export Format
                        </label>
                        <select id="format"
                                name="format"
                                class="form-select @error('format') error @enderror"
                                required
                                aria-describedby="@error('format') format-error @enderror format-help">
                            <option value="">Select Format</option>
                            <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>
                                PDF Document
                            </option>
                            <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>
                                Excel Spreadsheet
                            </option>
                        </select>
                        @error('format')
                            <div id="format-error" class="form-error" role="alert">
                                <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="format-help" class="form-help">
                            Choose your preferred file format for the report.
                        </div>
                    </div>

                    <!-- Information Alert -->
                    <div class="alert alert-info">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle mr-3 text-blue-600 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <p class="font-medium mb-1">Report Contents</p>
                                <p class="text-sm">Reports will include user information, registration dates, verification status, and activity data.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button type="submit"
                            class="btn btn-primary w-full"
                            aria-label="Generate and download report">
                        <i class="fas fa-download mr-2" aria-hidden="true"></i>
                        Generate & Download Report
                    </button>
                </form>
            </div>
        </div>

        <!-- Report Analytics -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie mr-2 text-blue-600" aria-hidden="true"></i>
                    Platform Analytics
                </h2>
                <p class="text-sm text-gray-600 mt-1">Current platform statistics and insights</p>
            </div>

            <div class="card-body">
                <!-- User Distribution Chart -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">User Distribution</h3>
                        <div class="space-y-3">
                            @php
                                $publicCount = \App\Models\UserRecord::where('user_type', 'public')->count();
                                $agencyCount = \App\Models\UserRecord::where('user_type', 'agency')->count();
                                $mcmcCount = \App\Models\UserRecord::where('user_type', 'mcmc')->count();
                                $totalUsers = $publicCount + $agencyCount + $mcmcCount;

                                $publicPercentage = $totalUsers > 0 ? ($publicCount / $totalUsers) * 100 : 0;
                                $agencyPercentage = $totalUsers > 0 ? ($agencyCount / $totalUsers) * 100 : 0;
                                $mcmcPercentage = $totalUsers > 0 ? ($mcmcCount / $totalUsers) * 100 : 0;
                            @endphp

                            <!-- Public Users -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Public Users</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $publicCount }}</span>
                                    <span class="text-xs text-gray-500">({{ number_format($publicPercentage, 1) }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $publicPercentage }}%"></div>
                            </div>

                            <!-- Agencies -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Agencies</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $agencyCount }}</span>
                                    <span class="text-xs text-gray-500">({{ number_format($agencyPercentage, 1) }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $agencyPercentage }}%"></div>
                            </div>

                            <!-- MCMC Staff -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">MCMC Staff</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $mcmcCount }}</span>
                                    <span class="text-xs text-gray-500">({{ number_format($mcmcPercentage, 1) }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $mcmcPercentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Quick Statistics</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</div>
                                <div class="text-xs text-gray-600">Total Users</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                @php
                                    $verifiedCount = \App\Models\UserRecord::whereNotNull('email_verified_at')->count();
                                    $verificationRate = $totalUsers > 0 ? ($verifiedCount / $totalUsers) * 100 : 0;
                                @endphp
                                <div class="text-2xl font-bold text-green-600">{{ number_format($verificationRate, 1) }}%</div>
                                <div class="text-xs text-gray-600">Verified</div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Instructions -->
                    <div class="alert alert-info">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle mr-3 text-blue-600 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <p class="font-medium mb-1">Report Generation</p>
                                <ul class="text-sm space-y-1">
                                    <li>• <strong>PDF Reports:</strong> Will open in a new tab. Use your browser's print function and select "Save as PDF"</li>
                                    <li>• <strong>Excel Reports:</strong> Will download as CSV files that can be opened in Excel</li>
                                    <li>• Reports include user data, registration dates, and verification status</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card mt-8">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-clock mr-2 text-gray-600" aria-hidden="true"></i>
                Recent Activity
            </h2>
            <p class="text-sm text-gray-600 mt-1">Latest user registrations and platform activity</p>
        </div>

        <div class="card-body">
            @php
                $recentUsers = \App\Models\UserRecord::latest()->take(5)->get();
            @endphp

            @if($recentUsers->count() > 0)
                <div class="space-y-4">
                    @foreach($recentUsers as $user)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($user->user_type === 'public')
                                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white" aria-hidden="true"></i>
                                        </div>
                                    @elseif($user->user_type === 'agency')
                                        <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-building text-white" aria-hidden="true"></i>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 bg-purple-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-tie text-white" aria-hidden="true"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($user->user_type) }} • {{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $user->created_at->format('M j, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $user->created_at->format('g:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-300 text-4xl mb-4" aria-hidden="true"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Recent Activity</h3>
                    <p class="text-gray-500">No recent user registrations to display.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="reports.generate"]');
        const reportTypeSelect = document.getElementById('report_type');
        const formatSelect = document.getElementById('format');

        if (form) {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');

                // Validate form
                if (!reportTypeSelect.value || !formatSelect.value) {
                    e.preventDefault();
                    alert('Please select both report type and format.');
                    return;
                }

                // For PDF reports, open in new tab
                if (formatSelect.value === 'pdf') {
                    e.preventDefault();

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span>Generating PDF...</span>
                        </div>
                    `;

                    // Create form data
                    const formData = new FormData(form);

                    // Submit form and open result in new tab
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        }
                        throw new Error('Network response was not ok');
                    })
                    .then(blob => {
                        // Create blob URL and open in new tab
                        const url = window.URL.createObjectURL(blob);
                        const newTab = window.open(url, '_blank');

                        if (newTab) {
                            // Show success message
                            showNotification('PDF report opened in new tab. Use Ctrl+P to save as PDF.', 'success');
                        } else {
                            showNotification('Please allow popups to view the PDF report.', 'warning');
                        }

                        // Clean up blob URL after a delay
                        setTimeout(() => window.URL.revokeObjectURL(url), 10000);
                    })
                    .catch(error => {
                        console.error('Error generating PDF:', error);
                        showNotification('Error generating PDF report. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Reset button
                        submitButton.disabled = false;
                        submitButton.innerHTML = `
                            <i class="fas fa-download mr-2" aria-hidden="true"></i>
                            Generate & Download Report
                        `;
                    });
                } else {
                    // For Excel reports, proceed normally
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span>Generating Excel...</span>
                        </div>
                    `;

                    // Re-enable button after 3 seconds
                    setTimeout(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = `
                            <i class="fas fa-download mr-2" aria-hidden="true"></i>
                            Generate & Download Report
                        `;
                    }, 3000);
                }
            });
        }

        // Dynamic form updates based on selection
        if (reportTypeSelect) {
            reportTypeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                const helpText = document.getElementById('report-type-help');

                switch(selectedType) {
                    case 'public_users':
                        helpText.textContent = 'Generate a report containing all public user registrations and their details.';
                        break;
                    case 'agencies':
                        helpText.textContent = 'Generate a report containing all registered agencies and their information.';
                        break;
                    case 'all':
                        helpText.textContent = 'Generate a comprehensive report containing all users across all categories.';
                        break;
                    default:
                        helpText.textContent = 'Choose the type of data to include in your report.';
                }
            });
        }
    });

    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;

        // Set colors based on type
        let bgColor, textColor, icon;
        switch(type) {
            case 'success':
                bgColor = 'bg-green-500';
                textColor = 'text-white';
                icon = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                icon = 'fas fa-exclamation-circle';
                break;
            case 'warning':
                bgColor = 'bg-yellow-500';
                textColor = 'text-white';
                icon = 'fas fa-exclamation-triangle';
                break;
            default:
                bgColor = 'bg-blue-500';
                textColor = 'text-white';
                icon = 'fas fa-info-circle';
        }

        notification.className += ` ${bgColor} ${textColor}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="${icon} mr-3" aria-hidden="true"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
</script>
@endpush
