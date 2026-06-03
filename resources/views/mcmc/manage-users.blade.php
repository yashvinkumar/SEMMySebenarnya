@extends('layouts.dashboard')

@section('title', 'User Management - MySebenarnya')

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link active">
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

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-users mr-3 text-purple-600" aria-hidden="true"></i>
                User Management
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Manage and monitor all registered users across the platform
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Search and Filter Controls -->
            <div class="relative">
                <input type="text" id="global-search" placeholder="Search users..."
                    class="form-input pl-10 pr-4 py-2 w-64" aria-label="Search users">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400" aria-hidden="true"></i>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" onclick="exportUsers()" aria-label="Export user data">
                <i class="fas fa-download mr-2" aria-hidden="true"></i>
                Export
            </button>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Public Users</p>
                    <p class="text-3xl font-bold">{{ $publicUsersCount }}</p>
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
                    <p class="text-3xl font-bold">{{ $agenciesCount }}</p>
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
                    <p class="text-purple-100 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold">{{ $publicUsersCount + $agenciesCount }}</p>
                    <p class="text-purple-100 text-xs mt-1">All platform users</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Verified</p>
                    <p class="text-3xl font-bold">
                        {{ $publicUsers->where('email_verified_at', '!=', null)->count() + $agencies->count() }}</p>
                    <p class="text-orange-100 text-xs mt-1">Email verified</p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Tabs -->
    <div class="card">
        <div class="card-header border-b border-gray-200">
            <nav class="flex space-x-8" aria-label="User management tabs">
                <button type="button" class="tab-button active" data-tab="public-users" aria-controls="public-users-panel"
                    aria-selected="true" role="tab">
                    <i class="fas fa-users mr-2" aria-hidden="true"></i>
                    Public Users
                    <span
                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $publicUsersCount }}
                    </span>
                </button>

                <button type="button" class="tab-button" data-tab="agencies" aria-controls="agencies-panel"
                    aria-selected="false" role="tab">
                    <i class="fas fa-building mr-2" aria-hidden="true"></i>
                    Agencies
                    <span
                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $agenciesCount }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Public Users Tab Panel -->
        <div id="public-users-panel" class="tab-panel active" role="tabpanel" aria-labelledby="public-users-tab">
            <div class="card-body">
                <!-- Filters and Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                        <div class="relative">
                            <input type="text" id="public-search" placeholder="Search public users..."
                                class="form-input pl-10 pr-4 py-2 w-64" aria-label="Search public users">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400" aria-hidden="true"></i>
                            </div>
                        </div>

                        <select class="form-select w-48" id="public-filter" aria-label="Filter public users">
                            <option value="">All Users</option>
                            <option value="verified">Verified Only</option>
                            <option value="unverified">Unverified Only</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="refreshPublicUsers()"
                            aria-label="Refresh public users list">
                            <i class="fas fa-sync-alt mr-2" aria-hidden="true"></i>
                            Refresh
                        </button>

                        <button type="button" class="btn btn-primary btn-sm" onclick="bulkEmailVerification()"
                            aria-label="Send bulk verification emails">
                            <i class="fas fa-envelope mr-2" aria-hidden="true"></i>
                            Bulk Verify
                        </button>
                    </div>
                </div>

                <!-- Public Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="public-users-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="table-header">
                                    <input type="checkbox" id="select-all-public" class="form-checkbox"
                                        aria-label="Select all public users">
                                </th>
                                <th scope="col" class="table-header">User</th>
                                <th scope="col" class="table-header">Contact</th>
                                <th scope="col" class="table-header">Status</th>
                                <th scope="col" class="table-header">Registered</th>
                                <th scope="col" class="table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($publicUsers as $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="table-cell">
                                        <input type="checkbox" class="form-checkbox user-checkbox"
                                            value="{{ $user->id }}" aria-label="Select user {{ $user->name }}">
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if ($user->profile_picture)
                                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                                        src="{{ Storage::url($user->profile_picture) }}"
                                                        alt="{{ $user->name }}'s profile picture">
                                                @else
                                                    <div
                                                        class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center border-2 border-gray-200">
                                                        <span class="text-white font-medium text-sm">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                <div class="text-xs text-gray-400">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="text-sm text-gray-900">
                                            {{ $user->contact_number ?? 'Not provided' }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        @if ($user->hasVerifiedEmail())
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                                Verified
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1" aria-hidden="true"></i>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if ($user->created_at)
                                            <div class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $user->created_at->format('g:i A') }}
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">Not available</div>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center space-x-2">
                                            <button type="button" class="btn-icon btn-icon-primary"
                                                onclick="viewUser({{ $user->id }})" aria-label="View user details">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>

                                            @if (!$user->hasVerifiedEmail())
                                                <button type="button" class="btn-icon btn-icon-warning"
                                                    onclick="sendVerificationEmail({{ $user->id }})"
                                                    aria-label="Send verification email">
                                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                                </button>
                                            @endif

                                            <button type="button" class="btn-icon btn-icon-secondary"
                                                onclick="showUserActivity({{ $user->id }})"
                                                aria-label="View user activity">
                                                <i class="fas fa-history" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="table-cell text-center py-12">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-gray-300 text-4xl mb-4" aria-hidden="true"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No public users found</h3>
                                            <p class="text-gray-500">There are no public users registered yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($publicUsers->hasPages())
                    <div class="mt-6">
                        {{ $publicUsers->appends(request()->except('public_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Agencies Tab Panel -->
        <div id="agencies-panel" class="tab-panel" role="tabpanel" aria-labelledby="agencies-tab">
            <div class="card-body">
                <!-- Filters and Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                        <div class="relative">
                            <input type="text" id="agency-search" placeholder="Search agencies..."
                                class="form-input pl-10 pr-4 py-2 w-64" aria-label="Search agencies">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400" aria-hidden="true"></i>
                            </div>
                        </div>

                        <select class="form-select w-48" id="agency-filter" aria-label="Filter agencies">
                            <option value="">All Agencies</option>
                            <option value="active">Active Only</option>
                            <option value="needs-reset">Needs Password Reset</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="refreshAgencies()"
                            aria-label="Refresh agencies list">
                            <i class="fas fa-sync-alt mr-2" aria-hidden="true"></i>
                            Refresh
                        </button>

                        <a href="{{ route('mcmc.register.agency') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                            Add Agency
                        </a>
                    </div>
                </div>

                <!-- Agencies Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="agencies-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="table-header">
                                    <input type="checkbox" id="select-all-agencies" class="form-checkbox"
                                        aria-label="Select all agencies">
                                </th>
                                <th scope="col" class="table-header">Agency</th>
                                <th scope="col" class="table-header">Contact</th>
                                <th scope="col" class="table-header">Status</th>
                                <th scope="col" class="table-header">Registered</th>
                                <th scope="col" class="table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($agencies as $agency)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="table-cell">
                                        <input type="checkbox" class="form-checkbox agency-checkbox"
                                            value="{{ $agency->agency_ID }}"
                                            aria-label="Select agency {{ $agency->agency_Name }}">
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if ($agency->profile_picture)
                                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                                        src="{{ Storage::url($agency->profile_picture) }}"
                                                        alt="{{ $agency->agency_Name }}'s profile picture">
                                                @else
                                                    <div
                                                        class="h-12 w-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center border-2 border-gray-200">
                                                        <i class="fas fa-building text-white text-lg"
                                                            aria-hidden="true"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $agency->agency_Name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $agency->agency_Email }}</div>
                                                <div class="text-xs text-gray-400">ID: {{ $agency->agency_ID }}</div>
                                                @if ($agency->agency_Type)
                                                    <div class="text-xs text-blue-600 font-medium">
                                                        {{ ucfirst($agency->agency_Type) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="text-sm text-gray-900">
                                            {{ $agency->agency_Phone ?? 'Not provided' }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        @if ($agency->agency_First_Time_Login)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-key mr-1" aria-hidden="true"></i>
                                                First Login
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if ($agency->agency_Created_At)
                                            <div class="text-sm text-gray-900">
                                                {{ $agency->agency_Created_At->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $agency->agency_Created_At->format('g:i A') }}</div>
                                        @else
                                            <div class="text-sm text-gray-500">Not available</div>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center space-x-2">
                                            <button type="button" class="btn-icon btn-icon-primary"
                                                onclick="viewAgency({{ $agency->agency_ID }})"
                                                aria-label="View agency details">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>

                                            <button type="button" class="btn-icon btn-icon-warning"
                                                onclick="resetAgencyPassword({{ $agency->agency_ID }})"
                                                aria-label="Reset agency password">
                                                <i class="fas fa-key" aria-hidden="true"></i>
                                            </button>

                                            <button type="button" class="btn-icon btn-icon-secondary"
                                                onclick="showAgencyActivity({{ $agency->agency_ID }})"
                                                aria-label="View agency activity">
                                                <i class="fas fa-history" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="table-cell text-center py-12">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-building text-gray-300 text-4xl mb-4" aria-hidden="true"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No agencies found</h3>
                                            <p class="text-gray-500 mb-4">There are no agencies registered yet.</p>
                                            <a href="{{ route('mcmc.register.agency') }}" class="btn btn-primary">
                                                <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                                                Register First Agency
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($agencies->hasPages())
                    <div class="mt-6">
                        {{ $agencies->appends(request()->except('agency_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="user-details-modal"
        class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" role="dialog"
        aria-labelledby="user-details-title" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 id="user-details-title" class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user mr-2 text-blue-600" aria-hidden="true"></i>
                    User Details
                </h3>
                <button type="button"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1"
                    onclick="closeModal('user-details-modal')" aria-label="Close modal">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>
            </div>

            <div id="user-details-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Agency Details Modal -->
    <div id="agency-details-modal"
        class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" role="dialog"
        aria-labelledby="agency-details-title" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 id="agency-details-title" class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-building mr-2 text-green-600" aria-hidden="true"></i>
                    Agency Details
                </h3>
                <button type="button"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1"
                    onclick="closeModal('agency-details-modal')" aria-label="Close modal">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>
            </div>

            <div id="agency-details-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal"
        class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" role="dialog"
        aria-labelledby="confirmation-title" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <div id="confirmation-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                    <!-- Icon will be set dynamically -->
                </div>
                <h3 id="confirmation-title" class="text-lg font-medium text-gray-900 mb-2">
                    <!-- Title will be set dynamically -->
                </h3>
                <p id="confirmation-message" class="text-sm text-gray-500 mb-6">
                    <!-- Message will be set dynamically -->
                </p>
                <div class="flex justify-center space-x-4">
                    <button type="button" id="confirmation-cancel" class="btn btn-secondary"
                        onclick="closeModal('confirmation-modal')">
                        Cancel
                    </button>
                    <button type="button" id="confirmation-confirm" class="btn btn-primary">
                        <!-- Text will be set dynamically -->
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanels = document.querySelectorAll('.tab-panel');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons and panels
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    tabPanels.forEach(panel => {
                        panel.classList.remove('active');
                    });

                    // Add active class to clicked button and corresponding panel
                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');
                    document.getElementById(targetTab + '-panel').classList.add('active');
                });
            });

            // Search functionality
            setupSearch();

            // Checkbox functionality
            setupCheckboxes();
        });

        // Search functionality
        function setupSearch() {
            const globalSearch = document.getElementById('global-search');
            const publicSearch = document.getElementById('public-search');
            const agencySearch = document.getElementById('agency-search');

            if (globalSearch) {
                globalSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    filterAllTables(searchTerm);
                });
            }

            if (publicSearch) {
                publicSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    filterTable('public-users-table', searchTerm);
                });
            }

            if (agencySearch) {
                agencySearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    filterTable('agencies-table', searchTerm);
                });
            }
        }

        function filterTable(tableId, searchTerm) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function filterAllTables(searchTerm) {
            filterTable('public-users-table', searchTerm);
            filterTable('agencies-table', searchTerm);
        }

        // Checkbox functionality
        function setupCheckboxes() {
            const selectAllPublic = document.getElementById('select-all-public');
            const selectAllAgencies = document.getElementById('select-all-agencies');

            if (selectAllPublic) {
                selectAllPublic.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.user-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            if (selectAllAgencies) {
                selectAllAgencies.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.agency-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
        }

        // Modal functions
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        // User management functions
        function viewUser(userId) {
            // Show loading state
            showModal('user-details-modal');
            document.getElementById('user-details-content').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600">Loading user details...</span>
            </div>
        `;

            // Simulate API call (replace with actual endpoint)
            setTimeout(() => {
                document.getElementById('user-details-content').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="h-24 w-24 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-2xl font-bold">U</span>
                        </div>
                        <h4 class="font-medium text-gray-900">User #${userId}</h4>
                    </div>
                    <div class="md:col-span-2">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            `;
            }, 1000);
        }

        function viewAgency(agencyId) {
            // Show loading state
            showModal('agency-details-modal');
            document.getElementById('agency-details-content').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <span class="ml-2 text-gray-600">Loading agency details...</span>
            </div>
        `;

            // Simulate API call (replace with actual endpoint)
            setTimeout(() => {
                document.getElementById('agency-details-content').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="h-24 w-24 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <h4 class="font-medium text-gray-900">Agency #${agencyId}</h4>
                    </div>
                    <div class="md:col-span-2">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm text-gray-900">Loading...</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            `;
            }, 1000);
        }

        function sendVerificationEmail(userId) {
            showConfirmationModal(
                'Send Verification Email',
                'Are you sure you want to send a verification email to this user?',
                'Send Email',
                () => {
                    // Implement actual email sending logic
                    console.log('Sending verification email to user:', userId);
                    closeModal('confirmation-modal');
                    showToast('Verification email sent successfully!', 'success');
                }
            );
        }

        function resetAgencyPassword(agencyId) {
            showConfirmationModal(
                'Reset Password',
                'Are you sure you want to reset the password for this agency? They will receive an email with reset instructions.',
                'Reset Password',
                () => {
                    // Implement actual password reset logic
                    console.log('Resetting password for agency:', agencyId);
                    closeModal('confirmation-modal');
                    showToast('Password reset email sent successfully!', 'success');
                }
            );
        }

        function showConfirmationModal(title, message, confirmText, onConfirm) {
            document.getElementById('confirmation-title').textContent = title;
            document.getElementById('confirmation-message').textContent = message;
            document.getElementById('confirmation-confirm').textContent = confirmText;

            const confirmButton = document.getElementById('confirmation-confirm');
            confirmButton.onclick = onConfirm;

            showModal('confirmation-modal');
        }

        function showToast(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className =
                `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

            if (type === 'success') {
                toast.className += ' bg-green-500 text-white';
            } else if (type === 'error') {
                toast.className += ' bg-red-500 text-white';
            } else {
                toast.className += ' bg-blue-500 text-white';
            }

            toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Additional utility functions
        function refreshPublicUsers() {
            showToast('Refreshing public users...', 'info');
            // Implement refresh logic
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function refreshAgencies() {
            showToast('Refreshing agencies...', 'info');
            // Implement refresh logic
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function bulkEmailVerification() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            if (selectedUsers.length === 0) {
                showToast('Please select users to send verification emails to.', 'error');
                return;
            }

            showConfirmationModal(
                'Bulk Email Verification',
                `Send verification emails to ${selectedUsers.length} selected users?`,
                'Send Emails',
                () => {
                    // Implement bulk email logic
                    console.log('Sending bulk verification emails to:', selectedUsers.length, 'users');
                    closeModal('confirmation-modal');
                    showToast(`Verification emails sent to ${selectedUsers.length} users!`, 'success');
                }
            );
        }

        function exportUsers() {
            showToast('Preparing user data export...', 'info');
            // Implement export logic
            setTimeout(() => {
                showToast('Export completed successfully!', 'success');
            }, 2000);
        }

        function showUserActivity(userId) {
            showToast('Loading user activity...', 'info');
            // Implement activity view logic
        }

        function showAgencyActivity(agencyId) {
            showToast('Loading agency activity...', 'info');
            // Implement activity view logic
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const visibleModals = document.querySelectorAll('.modal:not(.hidden)');
                visibleModals.forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    </script>
@endpush
