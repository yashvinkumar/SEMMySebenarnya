@extends('layouts.dashboard')

@section('title', 'Register Agency - MySebenarnya')

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2" aria-hidden="true"></i>
        Manage Users
    </a>
    <a href="{{ route('mcmc.register.agency') }}" class="nav-link active">
        <i class="fas fa-building mr-2" aria-hidden="true"></i>
        Register Agency
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

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-building mr-3 text-blue-600" aria-hidden="true"></i>
                Register New Agency
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Add a new government agency to the MySebenarnya platform
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

    <div class="max-w-4xl mx-auto">
        <!-- Registration Form -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-plus-circle mr-2 text-blue-600" aria-hidden="true"></i>
                    Agency Registration Form
                </h2>
                <p class="text-sm text-gray-600 mt-1">Complete all required fields to register a new agency</p>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('mcmc.register.agency') }}" class="space-y-6" novalidate>
                    @csrf

                    <!-- Agency Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-600" aria-hidden="true"></i>
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Agency Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="form-label">
                                    <i class="fas fa-building mr-2 text-gray-400" aria-hidden="true"></i>
                                    Agency Name
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-input @error('name') error @enderror"
                                       placeholder="Enter the full agency name"
                                       required
                                       aria-describedby="@error('name') name-error @enderror name-help">
                                @error('name')
                                    <div id="name-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="name-help" class="form-help">
                                    Enter the official name of the government agency.
                                </div>
                            </div>

                            <!-- Agency Type -->
                            <div class="md:col-span-2">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tags mr-2 text-gray-400" aria-hidden="true"></i>
                                    Agency Type
                                </label>
                                <select id="type"
                                        name="type"
                                        class="form-input @error('type') error @enderror"
                                        required
                                        aria-describedby="@error('type') type-error @enderror type-help">
                                    <option value="">Select agency type</option>
                                    <option value="ministry" {{ old('type') == 'ministry' ? 'selected' : '' }}>Ministry</option>
                                    <option value="department" {{ old('type') == 'department' ? 'selected' : '' }}>Department</option>
                                    <option value="statutory_body" {{ old('type') == 'statutory_body' ? 'selected' : '' }}>Statutory Body</option>
                                    <option value="government_agency" {{ old('type') == 'government_agency' ? 'selected' : '' }}>Government Agency</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                    <div id="type-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="type-help" class="form-help">
                                    Select the type that best describes this agency.
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope mr-2 text-gray-400" aria-hidden="true"></i>
                                    Official Email Address
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-input @error('email') error @enderror"
                                       placeholder="agency@gov.my"
                                       required
                                       aria-describedby="@error('email') email-error @enderror email-help">
                                @error('email')
                                    <div id="email-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="email-help" class="form-help">
                                    Use the official government email address for this agency.
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone mr-2 text-gray-400" aria-hidden="true"></i>
                                    Contact Number
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       class="form-input @error('phone') error @enderror"
                                       placeholder="+60123456789"
                                       required
                                       aria-describedby="@error('phone') contact-error @enderror contact-help">
                                @error('phone')
                                    <div id="contact-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="contact-help" class="form-help">
                                    Include country code (e.g., +60 for Malaysia).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-shield-alt mr-2 text-green-600" aria-hidden="true"></i>
                            Security Settings
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                                    Password
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-input @error('password') error @enderror"
                                       placeholder="Enter a secure password"
                                       required
                                       aria-describedby="@error('password') password-error @enderror password-help">
                                @error('password')
                                    <div id="password-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="password-help" class="form-help">
                                    Password must be at least 8 characters with letters, numbers, and symbols.
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                                    Confirm Password
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-input"
                                       placeholder="Confirm the password"
                                       required
                                       aria-describedby="password-confirm-help">
                                <div id="password-confirm-help" class="form-help">
                                    Re-enter the password to confirm.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-clipboard-list mr-2 text-purple-600" aria-hidden="true"></i>
                            Additional Information
                        </h3>

                        <!-- Agency Description -->
                        <div>
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left mr-2 text-gray-400" aria-hidden="true"></i>
                                Agency Description (Optional)
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="form-textarea @error('description') error @enderror"
                                      placeholder="Brief description of the agency's role and responsibilities"
                                      aria-describedby="@error('description') description-error @enderror description-help">{{ old('description') }}</textarea>
                            @error('description')
                                <div id="description-error" class="form-error" role="alert">
                                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="description-help" class="form-help">
                                Provide a brief overview of the agency's functions and services.
                            </div>
                        </div>
                    </div>

                    <!-- Important Notice -->
                    <div class="alert alert-warning">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mr-3 text-yellow-600 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <p class="font-medium mb-1">Important Notice</p>
                                <ul class="text-sm space-y-1">
                                    <li>• The agency will receive login credentials via the provided email address</li>
                                    <li>• Agency administrators should change the default password upon first login</li>
                                    <li>• All agency information will be verified before activation</li>
                                    <li>• Contact MCMC support if you need assistance with agency setup</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200">
                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                            <a href="{{ route('mcmc.users') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                                Back to Users
                            </a>
                        </div>

                        <div class="flex items-center space-x-4">
                            <button type="reset"
                                    class="btn btn-outline"
                                    onclick="return confirm('Are you sure you want to clear all form data?')">
                                <i class="fas fa-undo mr-2" aria-hidden="true"></i>
                                Reset Form
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    id="submit-button">
                                <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i>
                                Register Agency
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Registration Guidelines -->
        <div class="card mt-8">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-lightbulb mr-2 text-yellow-600" aria-hidden="true"></i>
                    Registration Guidelines
                </h2>
            </div>

            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">
                            <i class="fas fa-check-circle mr-2 text-green-600" aria-hidden="true"></i>
                            Requirements
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i class="fas fa-dot-circle mr-2 text-green-600 mt-1 text-xs" aria-hidden="true"></i>
                                Official government agency status
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-dot-circle mr-2 text-green-600 mt-1 text-xs" aria-hidden="true"></i>
                                Valid government email domain
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-dot-circle mr-2 text-green-600 mt-1 text-xs" aria-hidden="true"></i>
                                Authorized personnel contact information
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-dot-circle mr-2 text-green-600 mt-1 text-xs" aria-hidden="true"></i>
                                Compliance with data protection policies
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">
                            <i class="fas fa-cog mr-2 text-blue-600" aria-hidden="true"></i>
                            Next Steps
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right mr-2 text-blue-600 mt-1 text-xs" aria-hidden="true"></i>
                                Agency verification process (1-2 business days)
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right mr-2 text-blue-600 mt-1 text-xs" aria-hidden="true"></i>
                                Email notification with login credentials
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right mr-2 text-blue-600 mt-1 text-xs" aria-hidden="true"></i>
                                Initial setup and configuration assistance
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right mr-2 text-blue-600 mt-1 text-xs" aria-hidden="true"></i>
                                Training and onboarding session
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submit-button');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirmation');

        // Form submission handling
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        <span>Registering Agency...</span>
                    </div>
                `;
            });
        }

        // Password confirmation validation
        if (confirmPasswordField) {
            confirmPasswordField.addEventListener('input', function() {
                const password = passwordField.value;
                const confirmPassword = this.value;

                if (confirmPassword && password !== confirmPassword) {
                    this.classList.add('error');

                    // Remove existing error message
                    const existingError = document.getElementById('password-confirm-error');
                    if (existingError) {
                        existingError.remove();
                    }

                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'password-confirm-error';
                    errorDiv.className = 'form-error';
                    errorDiv.setAttribute('role', 'alert');
                    errorDiv.innerHTML = `
                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                        Passwords do not match
                    `;
                    this.parentNode.insertBefore(errorDiv, document.getElementById('password-confirm-help'));
                } else {
                    this.classList.remove('error');
                    const existingError = document.getElementById('password-confirm-error');
                    if (existingError) {
                        existingError.remove();
                    }
                }
            });
        }

        // Email domain validation
        const emailField = document.getElementById('email');
        if (emailField) {
            emailField.addEventListener('blur', function() {
                const email = this.value.toLowerCase();
                const govDomains = ['.gov.my', '.gov.', '.ministry.', '.jabatan.'];
                const isGovEmail = govDomains.some(domain => email.includes(domain));

                if (email && !isGovEmail) {
                    // Show warning (not error, as it might be valid)
                    const helpText = document.getElementById('email-help');
                    if (helpText) {
                        helpText.innerHTML = `
                            <i class="fas fa-exclamation-triangle mr-1 text-yellow-600" aria-hidden="true"></i>
                            <span class="text-yellow-700">Please ensure this is an official government email address.</span>
                        `;
                        helpText.classList.add('text-yellow-700');
                    }
                } else {
                    const helpText = document.getElementById('email-help');
                    if (helpText) {
                        helpText.innerHTML = 'Use the official government email address for this agency.';
                        helpText.classList.remove('text-yellow-700');
                    }
                }
            });
        }

        // Phone number formatting
        const phoneField = document.getElementById('contact_number');
        if (phoneField) {
            phoneField.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, ''); // Remove non-digits

                // Add +60 prefix if not present and starts with Malaysian numbers
                if (value.length > 0 && !value.startsWith('60') && (value.startsWith('1') || value.startsWith('3'))) {
                    value = '60' + value;
                }

                // Format with + prefix
                if (value.length > 0) {
                    this.value = '+' + value;
                }
            });
        }

        // Character counter for description
        const descriptionField = document.getElementById('description');
        if (descriptionField) {
            const maxLength = 500;

            // Create character counter
            const counter = document.createElement('div');
            counter.className = 'text-xs text-gray-500 mt-1';
            counter.id = 'description-counter';
            descriptionField.parentNode.appendChild(counter);

            function updateCounter() {
                const remaining = maxLength - descriptionField.value.length;
                counter.textContent = `${remaining} characters remaining`;

                if (remaining < 50) {
                    counter.classList.add('text-yellow-600');
                    counter.classList.remove('text-gray-500');
                } else {
                    counter.classList.add('text-gray-500');
                    counter.classList.remove('text-yellow-600');
                }
            }

            descriptionField.addEventListener('input', updateCounter);
            descriptionField.setAttribute('maxlength', maxLength);
            updateCounter(); // Initial count
        }
    });
</script>
@endpush
