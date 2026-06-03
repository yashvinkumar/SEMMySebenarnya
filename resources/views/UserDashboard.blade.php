@extends('layouts.dashboard')

@section('title')
    @if (isset($user) && $user->user_type)
        @switch($user->user_type)
            @case('public')
                Public User Dashboard - MySebenarnya
            @break

            @case('mcmc')
                MCMC Staff Dashboard - MySebenarnya
            @break

            @case('agency')
                Agency Dashboard - MySebenarnya
            @break

            @default
                User Dashboard - MySebenarnya
        @endswitch
    @else
        User Dashboard - MySebenarnya
    @endif
@endsection

@section('nav-links')
    @if (isset($user) && $user->user_type === 'mcmc')
        <a href="{{ route('mcmc.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link">
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
    @elseif (isset($user) && $user->user_type === 'agency')
        <a href="{{ route('agency.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt mr-2" aria-hidden="true"></i>
            Applications
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
            Analytics
        </a>
    @else
        <a href="{{ route('public.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="{{ route('inquiry.history') }}" class="nav-link">
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
    @endif
@endsection

@section('mobile-nav-links')
    @if (isset($user) && $user->user_type === 'mcmc')
        <a href="{{ route('mcmc.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link">
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
    @elseif (isset($user) && $user->user_type === 'agency')
        <a href="{{ route('agency.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt mr-2" aria-hidden="true"></i>
            Applications
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
            Analytics
        </a>
    @else
        <a href="{{ route('public.dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="{{ route('inquiry.history') }}" class="nav-link">
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
    @endif
@endsection

@section('user-menu-items')
    @if (isset($user) && $user->user_type)
        @switch($user->user_type)
            @case('public')
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
            @break

            @case('mcmc')
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
            @break

            @case('agency')
                <a href="{{ route('agency.profile') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
                    <i class="fas fa-user mr-3" aria-hidden="true"></i>
                    Profile
                </a>
                <a href="{{ route('agency.settings') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
                    <i class="fas fa-cog mr-3" aria-hidden="true"></i>
                    Settings
                </a>
            @break
        @endswitch
    @endif
@endsection

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if (isset($user) && $user->user_type)
                    @switch($user->user_type)
                        @case('public')
                            <i class="fas fa-users mr-3 text-blue-600" aria-hidden="true"></i>Public User Dashboard
                        @break

                        @case('mcmc')
                            <i class="fas fa-user-tie mr-3 text-purple-600" aria-hidden="true"></i>MCMC Staff Dashboard
                        @break

                        @case('agency')
                            <i class="fas fa-building mr-3 text-green-600" aria-hidden="true"></i>Agency Dashboard
                        @break

                        @default
                            <i class="fas fa-tachometer-alt mr-3 text-gray-600" aria-hidden="true"></i>User Dashboard
                    @endswitch
                @else
                    <i class="fas fa-tachometer-alt mr-3 text-gray-600" aria-hidden="true"></i>User Dashboard
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Welcome back, {{ $user->name ?? 'User' }}! Here's what's happening today.
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-sm font-medium text-gray-900">{{ now()->format('l, F j, Y') }}</div>
                <div class="text-xs text-gray-500">{{ now()->format('g:i A') }}</div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-8 border border-blue-100">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    @if (isset($user) && $user->user_type)
                        @switch($user->user_type)
                            @case('public')
                                Welcome to your personal dashboard
                            @break

                            @case('mcmc')
                                MCMC Staff Control Center
                            @break

                            @case('agency')
                                Agency Management Hub
                            @break

                            @default
                                Welcome to your dashboard
                        @endswitch
                    @else
                        Welcome to your dashboard
                    @endif
                </h2>
                <p class="text-gray-600">
                    @if (isset($user) && $user->user_type)
                        @switch($user->user_type)
                            @case('public')
                                Manage your account and access public services with ease.
                            @break

                            @case('mcmc')
                                Monitor system activity and manage users across the platform.
                            @break

                            @case('agency')
                                Oversee your organization's account and service management.
                            @break

                            @default
                                Manage your account and view your information.
                        @endswitch
                    @else
                        Manage your account and view your information.
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <div
                    class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                    @if (isset($user) && $user->user_type)
                        @switch($user->user_type)
                            @case('public')
                                <i class="fas fa-users text-white text-2xl" aria-hidden="true"></i>
                            @break

                            @case('mcmc')
                                <i class="fas fa-user-tie text-white text-2xl" aria-hidden="true"></i>
                            @break

                            @case('agency')
                                <i class="fas fa-building text-white text-2xl" aria-hidden="true"></i>
                            @break

                            @default
                                <i class="fas fa-tachometer-alt text-white text-2xl" aria-hidden="true"></i>
                        @endswitch
                    @else
                        <i class="fas fa-tachometer-alt text-white text-2xl" aria-hidden="true"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (isset($user) && $user->user_type === 'mcmc')
        <!-- MCMC Staff Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Public Users</p>
                        <p class="text-3xl font-bold">{{ $publicUsersCount ?? 0 }}</p>
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
                        <p class="text-3xl font-bold">{{ $agenciesCount ?? 0 }}</p>
                        <p class="text-green-100 text-xs mt-1">Registered organizations</p>
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
                        <p class="text-3xl font-bold">{{ ($publicUsersCount ?? 0) + ($agenciesCount ?? 0) }}</p>
                        <p class="text-purple-100 text-xs mt-1">All platform users</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-chart-line text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- MCMC Management Tools -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">User Management</h3>
                    <div
                        class="bg-blue-100 text-blue-600 rounded-full p-2 group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-users text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Manage public users and agencies across the platform.</p>
                <a href="{{ route('mcmc.users') }}" class="btn btn-primary w-full">
                    <i class="fas fa-arrow-right mr-2" aria-hidden="true"></i>
                    Manage Users
                </a>
            </div>

            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Agency Registration</h3>
                    <div
                        class="bg-green-100 text-green-600 rounded-full p-2 group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-building text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Register new agencies and organizations.</p>
                <a href="{{ route('mcmc.register.agency') }}" class="btn btn-success w-full">
                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                    Register Agency
                </a>
            </div>

            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">System Reports</h3>
                    <div
                        class="bg-purple-100 text-purple-600 rounded-full p-2 group-hover:bg-purple-200 transition-colors duration-200">
                        <i class="fas fa-chart-bar text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Generate and view system analytics and reports.</p>
                <a href="{{ route('mcmc.reports.index') }}" class="btn btn-purple w-full">
                    <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
                    View Reports
                </a>
            </div>

            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">User Reports</h3>
                    <div
                        class="bg-orange-100 text-orange-600 rounded-full p-2 group-hover:bg-orange-200 transition-colors duration-200">
                        <i class="fas fa-file-export text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Export user data to PDF or Excel format.</p>
                <button onclick="showUserReportModal()" class="btn btn-warning w-full">
                    <i class="fas fa-download mr-2" aria-hidden="true"></i>
                    Report Users
                </button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent System Activity</h3>
                <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-green-100 text-green-600 rounded-full p-2">
                        <i class="fas fa-user-plus text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New public user registered</p>
                        <p class="text-xs text-gray-500">2 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                        <i class="fas fa-building text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Agency account updated</p>
                        <p class="text-xs text-gray-500">4 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-purple-100 text-purple-600 rounded-full p-2">
                        <i class="fas fa-chart-line text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Monthly report generated</p>
                        <p class="text-xs text-gray-500">1 day ago</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif (isset($user) && $user->user_type === 'agency')
        <!-- Agency Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Active Applications</p>
                        <p class="text-3xl font-bold">12</p>
                        <p class="text-green-100 text-xs mt-1">In progress</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-file-alt text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold">45</p>
                        <p class="text-blue-100 text-xs mt-1">This month</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Success Rate</p>
                        <p class="text-3xl font-bold">94%</p>
                        <p class="text-purple-100 text-xs mt-1">Overall performance</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-chart-line text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agency Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">New Application</h3>
                    <div
                        class="bg-green-100 text-green-600 rounded-full p-2 group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-plus text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Submit a new application or request.</p>
                <a href="#" class="btn btn-success w-full">
                    <i class="fas fa-file-plus mr-2" aria-hidden="true"></i>
                    Create Application
                </a>
            </div>

            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">View Analytics</h3>
                    <div
                        class="bg-blue-100 text-blue-600 rounded-full p-2 group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-chart-bar text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Track your organization's performance metrics.</p>
                <a href="#" class="btn btn-primary w-full">
                    <i class="fas fa-analytics mr-2" aria-hidden="true"></i>
                    View Analytics
                </a>
            </div>
        </div>
    @else
        <!-- Public User Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">My Inquiries</p>
                        <p class="text-3xl font-bold">3</p>
                        <p class="text-blue-100 text-xs mt-1">Active requests</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-clipboard-list text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold">8</p>
                        <p class="text-green-100 text-xs mt-1">Total resolved</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Services Used</p>
                        <p class="text-3xl font-bold">5</p>
                        <p class="text-purple-100 text-xs mt-1">Different categories</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-star text-2xl" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Public User Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Submit New Inquiry</h3>
                    <div
                        class="bg-blue-100 text-blue-600 rounded-full p-2 group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-plus text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Submit a new inquiry or request for assistance.</p>
                <a href="{{ route('inquiry.create') }}" class="btn btn-primary w-full">
                    <i class="fas fa-file-plus mr-2" aria-hidden="true"></i>
                    Create Inquiry
                </a>
            </div>

            <div class="card group">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">View My Inquiries</h3>
                    <div
                        class="bg-green-100 text-green-600 rounded-full p-2 group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-list text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">Track the status of your submitted inquiries.</p>
                <a href="{{ route('inquiry.history') }}" class="btn btn-success w-full">
                    <i class="fas fa-clipboard-list mr-2" aria-hidden="true"></i>
                    View Inquiries
                </a>
            </div>
        </div>

        <!-- Recent Inquiries -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Inquiries</h3>
                <a href="{{ route('inquiry.history') }}"
                    class="text-blue-600 hover:text-blue-500 text-sm font-medium">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-yellow-100 text-yellow-600 rounded-full p-2">
                        <i class="fas fa-clock text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Document Verification Request</p>
                        <p class="text-xs text-gray-500">Status: Pending • 2 days ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                        <i class="fas fa-info-circle text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">General Information Request</p>
                        <p class="text-xs text-gray-500">Status: In Progress • 5 days ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="bg-green-100 text-green-600 rounded-full p-2">
                        <i class="fas fa-check-circle text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Service Application</p>
                        <p class="text-xs text-gray-500">Status: Completed • 1 week ago</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (isset($user) && $user->user_type === 'mcmc')
        <!-- User Report Modal -->
        <div id="userReportModal"
            class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" role="dialog"
            aria-labelledby="userReportTitle" aria-hidden="true">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900" id="userReportTitle">
                            <i class="fas fa-file-export mr-2 text-orange-600"></i>
                            Generate User Report
                        </h3>
                        <button type="button" onclick="closeUserReportModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="space-y-6">
                        <div class="text-center">
                            <div class="bg-orange-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                                <i class="fas fa-users text-2xl text-orange-600"></i>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Export a comprehensive list of all users in the system including public users and agencies.
                            </p>
                        </div>

                        <!-- Report Options -->
                        <div class="space-y-4">
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 text-red-600 rounded-full p-2 mr-3">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">PDF Report</h4>
                                            <p class="text-sm text-gray-500">Formatted document with user details</p>
                                        </div>
                                    </div>
                                    <button onclick="generateUserReport('pdf')" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-download mr-1"></i>
                                        PDF
                                    </button>
                                </div>
                            </div>

                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 text-green-600 rounded-full p-2 mr-3">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Excel Report</h4>
                                            <p class="text-sm text-gray-500">Spreadsheet format for data analysis</p>
                                        </div>
                                    </div>
                                    <button onclick="generateUserReport('excel')" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download mr-1"></i>
                                        Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Report Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 text-blue-600 rounded-full p-1 mr-3 mt-0.5">
                                    <i class="fas fa-info text-sm"></i>
                                </div>
                                <div>
                                    <h5 class="font-medium text-blue-900 mb-1">Report Contents</h5>
                                    <ul class="text-sm text-blue-700 space-y-1">
                                        <li>• Public user information (name, email, registration date)</li>
                                        <li>• Agency details (name, type, contact information)</li>
                                        <li>• User status and verification information</li>
                                        <li>• Generated on {{ now()->format('M j, Y \a\t g:i A') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <button type="button" onclick="closeUserReportModal()" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showUserReportModal() {
                document.getElementById('userReportModal').classList.remove('hidden');
                document.getElementById('userReportModal').setAttribute('aria-hidden', 'false');
            }

            function closeUserReportModal() {
                document.getElementById('userReportModal').classList.add('hidden');
                document.getElementById('userReportModal').setAttribute('aria-hidden', 'true');
            }

            function generateUserReport(format) {
                // Show loading state
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Generating...';
                button.disabled = true;

                // Use fetch API for better error handling
                fetch('{{ route('mcmc.users.report') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: 'format=' + encodeURIComponent(format)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;

                        // Set filename based on format
                        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
                        const extension = format === 'pdf' ? 'html' : 'csv';
                        a.download = `user-report-${timestamp}.${extension}`;

                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Reset button and close modal
                        button.innerHTML = originalContent;
                        button.disabled = false;
                        closeUserReportModal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to generate report. Please try again.');

                        // Reset button
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    });
            }

            // Close modal when clicking outside
            document.getElementById('userReportModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeUserReportModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !document.getElementById('userReportModal').classList.contains('hidden')) {
                    closeUserReportModal();
                }
            });
        </script>
    @endif
@endsection
