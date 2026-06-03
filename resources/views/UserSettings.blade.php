@extends('layouts.dashboard')

@section('title')
    @if (isset($user) && $user->user_type)
        @switch($user->user_type)
            @case('public')
                Public User Settings - MySebenarnya
            @break

            @case('mcmc')
                MCMC Staff Settings - MySebenarnya
            @break

            @case('agency')
                Agency Settings - MySebenarnya
            @break

            @default
                User Settings - MySebenarnya
        @endswitch
    @else
        User Settings - MySebenarnya
    @endif
@endsection

@section('nav-links')
    @if (isset($user) && $user->user_type === 'mcmc')
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
        <a href="{{ route('mcmc.reports.index') }}" class="nav-link">
            <i class="fas fa-chart-bar mr-2" aria-hidden="true"></i>
            Reports
        </a>
    @elseif (isset($user) && $user->user_type === 'agency')
        <a href="{{ route('agency.dashboard') }}" class="nav-link">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt mr-2" aria-hidden="true"></i>
            Applications
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
            Reports
        </a>
    @else
        <a href="{{ route('public.dashboard') }}" class="nav-link">
            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
            Dashboard
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt mr-2" aria-hidden="true"></i>
            My Applications
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-bell mr-2" aria-hidden="true"></i>
            Notifications
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
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200 bg-gray-50"
                    role="menuitem">
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
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200 bg-gray-50"
                    role="menuitem">
                    <i class="fas fa-cog mr-3" aria-hidden="true"></i>
                    Settings
                </a>
            @break

            @case('agency')
                <a href="{{ route('agency.profile') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
                    <i class="fas fa-building mr-3" aria-hidden="true"></i>
                    Agency Profile
                </a>
                <a href="{{ route('agency.settings') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200 bg-gray-50"
                    role="menuitem">
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
                <i class="fas fa-cog mr-3 text-blue-600" aria-hidden="true"></i>
                @if (isset($user) && $user->user_type)
                    @switch($user->user_type)
                        @case('public')
                            Account Settings
                        @break

                        @case('mcmc')
                            MCMC Staff Settings
                        @break

                        @case('agency')
                            Agency Settings
                        @break

                        @default
                            Account Settings
                    @endswitch
                @else
                    Account Settings
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Manage your account preferences, security settings, and notifications
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Back
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

    @if (session('error'))
        <div class="alert alert-error mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-600" aria-hidden="true"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <!-- Settings Navigation Tabs -->
        <div class="card mb-8">
            <div class="card-header border-b border-gray-200">
                <nav class="flex space-x-8" aria-label="Settings tabs">
                    <button type="button" class="tab-button active" data-tab="general" aria-controls="general-panel"
                        aria-selected="true" role="tab">
                        <i class="fas fa-user mr-2" aria-hidden="true"></i>
                        General
                    </button>

                    <button type="button" class="tab-button" data-tab="security" aria-controls="security-panel"
                        aria-selected="false" role="tab">
                        <i class="fas fa-shield-alt mr-2" aria-hidden="true"></i>
                        Security
                    </button>

                    <button type="button" class="tab-button" data-tab="notifications"
                        aria-controls="notifications-panel" aria-selected="false" role="tab">
                        <i class="fas fa-bell mr-2" aria-hidden="true"></i>
                        Notifications
                    </button>

                    <button type="button" class="tab-button" data-tab="privacy" aria-controls="privacy-panel"
                        aria-selected="false" role="tab">
                        <i class="fas fa-lock mr-2" aria-hidden="true"></i>
                        Privacy
                    </button>
                </nav>
            </div>
        </div>

        <!-- General Settings Tab -->
        <div id="general-panel" class="tab-panel active" role="tabpanel" aria-labelledby="general-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-blue-600" aria-hidden="true"></i>
                        General Settings
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Update your basic account information and preferences</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update.general') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Display Name -->
                        <div>
                            <label for="display_name" class="form-label">
                                <i class="fas fa-id-card mr-2 text-gray-400" aria-hidden="true"></i>
                                Display Name
                            </label>
                            <input type="text" id="display_name" name="display_name"
                                value="{{ old('display_name', $user->name ?? '') }}"
                                class="form-input @error('display_name') error @enderror"
                                placeholder="How your name appears to others" required
                                aria-describedby="@error('display_name') display-name-error @enderror display-name-help">
                            @error('display_name')
                                <div id="display-name-error" class="form-error" role="alert">
                                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="display-name-help" class="form-help">
                                This name will be visible to other users and in communications.
                            </div>
                        </div>

                        <!-- Language Preference -->
                        <div>
                            <label for="language" class="form-label">
                                <i class="fas fa-language mr-2 text-gray-400" aria-hidden="true"></i>
                                Preferred Language
                            </label>
                            <select id="language" name="language" class="form-select @error('language') error @enderror"
                                aria-describedby="@error('language') language-error @enderror language-help">
                                <option value="en"
                                    {{ old('language', $user->language ?? 'en') === 'en' ? 'selected' : '' }}>
                                    English
                                </option>
                                <option value="ms"
                                    {{ old('language', $user->language ?? 'en') === 'ms' ? 'selected' : '' }}>
                                    Bahasa Malaysia
                                </option>
                            </select>
                            @error('language')
                                <div id="language-error" class="form-error" role="alert">
                                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="language-help" class="form-help">
                                Choose your preferred language for the interface and communications.
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="form-label">
                                <i class="fas fa-clock mr-2 text-gray-400" aria-hidden="true"></i>
                                Timezone
                            </label>
                            <select id="timezone" name="timezone" class="form-select @error('timezone') error @enderror"
                                aria-describedby="@error('timezone') timezone-error @enderror timezone-help">
                                <option value="Asia/Kuala_Lumpur"
                                    {{ old('timezone', $user->timezone ?? 'Asia/Kuala_Lumpur') === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>
                                    Malaysia Time (GMT+8)
                                </option>
                                <option value="Asia/Singapore"
                                    {{ old('timezone', $user->timezone ?? 'Asia/Kuala_Lumpur') === 'Asia/Singapore' ? 'selected' : '' }}>
                                    Singapore Time (GMT+8)
                                </option>
                                <option value="UTC"
                                    {{ old('timezone', $user->timezone ?? 'Asia/Kuala_Lumpur') === 'UTC' ? 'selected' : '' }}>
                                    UTC (GMT+0)
                                </option>
                            </select>
                            @error('timezone')
                                <div id="timezone-error" class="form-error" role="alert">
                                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="timezone-help" class="form-help">
                                All dates and times will be displayed in your selected timezone.
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2" aria-hidden="true"></i>
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Settings Tab -->
        <div id="security-panel" class="tab-panel" role="tabpanel" aria-labelledby="security-tab">
            <div class="space-y-6">
                <!-- Change Password -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-key mr-2 text-green-600" aria-hidden="true"></i>
                            Change Password
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Update your account password for better security</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update.password') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                                    Current Password
                                </label>
                                <input type="password" id="current_password" name="current_password"
                                    class="form-input @error('current_password') error @enderror"
                                    placeholder="Enter your current password" required
                                    aria-describedby="@error('current_password') current-password-error @enderror">
                                @error('current_password')
                                    <div id="current-password-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                                    New Password
                                </label>
                                <input type="password" id="new_password" name="new_password"
                                    class="form-input @error('new_password') error @enderror"
                                    placeholder="Enter your new password" required
                                    aria-describedby="@error('new_password') new-password-error @enderror new-password-help">
                                @error('new_password')
                                    <div id="new-password-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="new-password-help" class="form-help">
                                    Password must be at least 8 characters with letters, numbers, and symbols.
                                </div>
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                                    Confirm New Password
                                </label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="form-input" placeholder="Confirm your new password" required>
                            </div>

                            <!-- Save Button -->
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key mr-2" aria-hidden="true"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-mobile-alt mr-2 text-purple-600" aria-hidden="true"></i>
                            Two-Factor Authentication
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Add an extra layer of security to your account</p>
                    </div>

                    <div class="card-body">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shield-alt text-2xl text-gray-400" aria-hidden="true"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h3>
                                    <p class="text-sm text-gray-600">Currently disabled</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline">
                                <i class="fas fa-plus mr-2" aria-hidden="true"></i>
                                Enable 2FA
                            </button>
                        </div>

                        <div class="alert alert-info mt-4">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle mr-3 text-blue-600 mt-0.5" aria-hidden="true"></i>
                                <div>
                                    <p class="font-medium mb-1">Enhanced Security</p>
                                    <p class="text-sm">Two-factor authentication adds an extra layer of security by
                                        requiring a verification code from your mobile device when signing in.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Settings Tab -->
        <div id="notifications-panel" class="tab-panel" role="tabpanel" aria-labelledby="notifications-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bell mr-2 text-yellow-600" aria-hidden="true"></i>
                        Notification Preferences
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Choose how and when you want to receive notifications</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update.notifications') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Email Notifications -->
                        <div>
                            <h3 class="text-base font-medium text-gray-900 mb-4">Email Notifications</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="email_application_updates"
                                            class="text-sm font-medium text-gray-700">Application Updates</label>
                                        <p class="text-sm text-gray-500">Receive emails about your application status
                                            changes</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="email_application_updates"
                                            name="email_application_updates" value="1"
                                            {{ old('email_application_updates', true) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="email_system_announcements"
                                            class="text-sm font-medium text-gray-700">System Announcements</label>
                                        <p class="text-sm text-gray-500">Important system updates and maintenance
                                            notifications</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="email_system_announcements"
                                            name="email_system_announcements" value="1"
                                            {{ old('email_system_announcements', true) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="email_security_alerts"
                                            class="text-sm font-medium text-gray-700">Security Alerts</label>
                                        <p class="text-sm text-gray-500">Login attempts and security-related notifications
                                        </p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="email_security_alerts" name="email_security_alerts"
                                            value="1" {{ old('email_security_alerts', true) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Notifications -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-base font-medium text-gray-900 mb-4">SMS Notifications</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="sms_urgent_updates" class="text-sm font-medium text-gray-700">Urgent
                                            Updates</label>
                                        <p class="text-sm text-gray-500">Critical application updates via SMS</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="sms_urgent_updates" name="sms_urgent_updates"
                                            value="1" {{ old('sms_urgent_updates', false) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="sms_security_alerts"
                                            class="text-sm font-medium text-gray-700">Security Alerts</label>
                                        <p class="text-sm text-gray-500">Login and security notifications via SMS</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="sms_security_alerts" name="sms_security_alerts"
                                            value="1" {{ old('sms_security_alerts', false) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end pt-6 border-t border-gray-200">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2" aria-hidden="true"></i>
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Privacy Settings Tab -->
        <div id="privacy-panel" class="tab-panel" role="tabpanel" aria-labelledby="privacy-tab">
            <div class="space-y-6">
                <!-- Data Privacy -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-shield mr-2 text-indigo-600" aria-hidden="true"></i>
                            Data Privacy
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Control how your data is used and shared</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update.privacy') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="profile_visibility" class="text-sm font-medium text-gray-700">Profile
                                            Visibility</label>
                                        <p class="text-sm text-gray-500">Allow other users to see your profile information
                                        </p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="profile_visibility" name="profile_visibility"
                                            value="1" {{ old('profile_visibility', true) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="data_analytics" class="text-sm font-medium text-gray-700">Usage
                                            Analytics</label>
                                        <p class="text-sm text-gray-500">Help improve our services by sharing anonymous
                                            usage data</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="data_analytics" name="data_analytics" value="1"
                                            {{ old('data_analytics', true) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="flex justify-end pt-6 border-t border-gray-200">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2" aria-hidden="true"></i>
                                    Save Privacy Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Management -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-times mr-2 text-red-600" aria-hidden="true"></i>
                            Account Management
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Manage your account data and deletion options</p>
                    </div>

                    <div class="card-body">
                        <div class="space-y-4">
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h3 class="text-sm font-medium text-yellow-800 mb-2">Download Your Data</h3>
                                <p class="text-sm text-yellow-700 mb-3">Request a copy of all your personal data stored in
                                    our system.</p>
                                <button type="button" class="btn btn-outline btn-sm">
                                    <i class="fas fa-download mr-2" aria-hidden="true"></i>
                                    Request Data Export
                                </button>
                            </div>

                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h3 class="text-sm font-medium text-red-800 mb-2">Delete Account</h3>
                                <p class="text-sm text-red-700 mb-3">Permanently delete your account and all associated
                                    data. This action cannot be undone.</p>
                                <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteAccountModal()">
                                    <i class="fas fa-trash mr-2" aria-hidden="true"></i>
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div id="delete-account-modal"
        class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" role="dialog"
        aria-labelledby="delete-account-title" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600" aria-hidden="true"></i>
                </div>
                <h3 id="delete-account-title" class="text-lg font-medium text-gray-900 mb-2">
                    Delete Account
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete your account? This action cannot be undone and all your data will be
                    permanently removed.
                </p>
                <div class="flex justify-center space-x-4">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('delete-account-modal')">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteAccount()">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
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
        });

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

        function showDeleteAccountModal() {
            showModal('delete-account-modal');
        }

        function deleteAccount() {
            // Implement account deletion logic
            alert('Account deletion functionality would be implemented here.');
            closeModal('delete-account-modal');
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
