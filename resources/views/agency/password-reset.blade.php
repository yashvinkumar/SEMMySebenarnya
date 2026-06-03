@extends('layouts.auth')

@section('title', 'Reset Password - MySebenarnya')
@section('page-title', 'Reset Your Password')
@section('page-subtitle', 'Set a new password to secure your agency account')

@section('content')
    <!-- Password Reset Required Alert -->
    <div class="alert alert-warning mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle mr-3 text-yellow-600 mt-0.5" aria-hidden="true"></i>
            <div>
                <p class="font-medium mb-1">Password Reset Required</p>
                <p class="text-sm">You must set a new password before you can access your agency account. This is a security requirement for all new agency registrations.</p>
            </div>
        </div>
    </div>

    <!-- Password Reset Form -->
    <form method="POST" action="{{ route('agency.password.reset') }}" class="space-y-6" novalidate>
        @csrf

        <!-- New Password Field -->
        <div>
            <label for="password" class="form-label">
                <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                New Password
            </label>
            <div class="relative">
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input @error('password') error @enderror pr-12"
                       placeholder="Enter your new password"
                       required
                       autocomplete="new-password"
                       aria-describedby="@error('password') password-error @enderror password-help password-strength">
                <button type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePasswordVisibility('password')"
                        aria-label="Toggle password visibility">
                    <i id="password-toggle-icon" class="fas fa-eye text-gray-400 hover:text-gray-600" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <div id="password-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="password-help" class="form-help">
                Password must be at least 8 characters long and contain a mix of letters, numbers, and symbols.
            </div>

            <!-- Password Strength Indicator -->
            <div id="password-strength" class="mt-2">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div id="strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <span id="strength-text" class="text-xs text-gray-500">Enter password</span>
                </div>
                <div id="strength-requirements" class="mt-2 space-y-1 text-xs">
                    <div id="req-length" class="flex items-center text-gray-500">
                        <i class="fas fa-circle mr-2 text-xs" aria-hidden="true"></i>
                        At least 8 characters
                    </div>
                    <div id="req-letter" class="flex items-center text-gray-500">
                        <i class="fas fa-circle mr-2 text-xs" aria-hidden="true"></i>
                        Contains letters
                    </div>
                    <div id="req-number" class="flex items-center text-gray-500">
                        <i class="fas fa-circle mr-2 text-xs" aria-hidden="true"></i>
                        Contains numbers
                    </div>
                    <div id="req-special" class="flex items-center text-gray-500">
                        <i class="fas fa-circle mr-2 text-xs" aria-hidden="true"></i>
                        Contains special characters
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                Confirm New Password
            </label>
            <div class="relative">
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input pr-12"
                       placeholder="Confirm your new password"
                       required
                       autocomplete="new-password"
                       aria-describedby="password-confirm-help">
                <button type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePasswordVisibility('password_confirmation')"
                        aria-label="Toggle password confirmation visibility">
                    <i id="password_confirmation-toggle-icon" class="fas fa-eye text-gray-400 hover:text-gray-600" aria-hidden="true"></i>
                </button>
            </div>
            <div id="password-confirm-help" class="form-help">
                Re-enter your password to confirm it matches.
            </div>
            <div id="password-match" class="mt-1 text-xs hidden">
                <div id="match-indicator" class="flex items-center">
                    <i id="match-icon" class="fas fa-circle mr-2 text-xs" aria-hidden="true"></i>
                    <span id="match-text">Passwords match</span>
                </div>
            </div>
        </div>

        <!-- Security Guidelines -->
        <div class="alert alert-info">
            <div class="flex items-start">
                <i class="fas fa-shield-alt mr-3 text-blue-600 mt-0.5" aria-hidden="true"></i>
                <div>
                    <p class="font-medium mb-1">Security Guidelines</p>
                    <ul class="text-sm space-y-1">
                        <li>• Use a unique password that you don't use elsewhere</li>
                        <li>• Consider using a password manager for better security</li>
                        <li>• Never share your password with anyone</li>
                        <li>• Change your password regularly for optimal security</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="btn btn-primary w-full"
                id="submit-button"
                disabled>
            <i class="fas fa-key mr-2" aria-hidden="true"></i>
            Set New Password
        </button>
    </form>

    <!-- Additional Help -->
    <div class="mt-8 text-center">
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                <i class="fas fa-question-circle mr-2 text-blue-600" aria-hidden="true"></i>
                Need Help?
            </h3>
            <p class="text-sm text-gray-600 mb-4">
                If you're experiencing issues with password reset or have questions about your agency account,
                please contact MCMC support for assistance.
            </p>
            <div class="flex flex-col sm:flex-row sm:justify-center sm:space-x-4 space-y-2 sm:space-y-0">
                <a href="mailto:support@mcmc.gov.my"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-envelope mr-2" aria-hidden="true"></i>
                    Email Support
                </a>
                <a href="tel:+60312345678"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <i class="fas fa-phone mr-2" aria-hidden="true"></i>
                    Call Support
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirmation');
        const submitButton = document.getElementById('submit-button');
        const form = document.querySelector('form');

        let passwordValid = false;
        let passwordsMatch = false;

        // Password strength checking
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
                updateSubmitButton();
            });
        }

        // Password confirmation checking
        if (confirmPasswordField) {
            confirmPasswordField.addEventListener('input', function() {
                checkPasswordMatch();
                updateSubmitButton();
            });
        }

        // Form submission
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                if (!passwordValid || !passwordsMatch) {
                    e.preventDefault();
                    return;
                }

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        <span>Setting Password...</span>
                    </div>
                `;
            });
        }

        function checkPasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                letter: /[a-zA-Z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Update requirement indicators
            updateRequirement('req-length', requirements.length);
            updateRequirement('req-letter', requirements.letter);
            updateRequirement('req-number', requirements.number);
            updateRequirement('req-special', requirements.special);

            // Calculate strength
            const metRequirements = Object.values(requirements).filter(Boolean).length;
            const strength = metRequirements / 4;

            // Update strength bar
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            if (strengthBar && strengthText) {
                strengthBar.style.width = (strength * 100) + '%';

                if (strength === 0) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300';
                    strengthText.textContent = 'Enter password';
                    strengthText.className = 'text-xs text-gray-500';
                } else if (strength < 0.5) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'text-xs text-red-600';
                } else if (strength < 0.75) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                    strengthText.textContent = 'Fair';
                    strengthText.className = 'text-xs text-yellow-600';
                } else if (strength < 1) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
                    strengthText.textContent = 'Good';
                    strengthText.className = 'text-xs text-blue-600';
                } else {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'text-xs text-green-600';
                }
            }

            passwordValid = strength >= 0.75; // Require at least "Good" strength
        }

        function updateRequirement(elementId, met) {
            const element = document.getElementById(elementId);
            if (element) {
                const icon = element.querySelector('i');
                if (met) {
                    element.className = 'flex items-center text-green-600';
                    icon.className = 'fas fa-check-circle mr-2 text-xs';
                } else {
                    element.className = 'flex items-center text-gray-500';
                    icon.className = 'fas fa-circle mr-2 text-xs';
                }
            }
        }

        function checkPasswordMatch() {
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;
            const matchIndicator = document.getElementById('password-match');
            const matchIcon = document.getElementById('match-icon');
            const matchText = document.getElementById('match-text');

            if (confirmPassword.length > 0) {
                matchIndicator.classList.remove('hidden');

                if (password === confirmPassword) {
                    passwordsMatch = true;
                    matchIndicator.className = 'mt-1 text-xs text-green-600';
                    matchIcon.className = 'fas fa-check-circle mr-2 text-xs';
                    matchText.textContent = 'Passwords match';
                    confirmPasswordField.classList.remove('error');
                } else {
                    passwordsMatch = false;
                    matchIndicator.className = 'mt-1 text-xs text-red-600';
                    matchIcon.className = 'fas fa-times-circle mr-2 text-xs';
                    matchText.textContent = 'Passwords do not match';
                    confirmPasswordField.classList.add('error');
                }
            } else {
                matchIndicator.classList.add('hidden');
                passwordsMatch = false;
                confirmPasswordField.classList.remove('error');
            }
        }

        function updateSubmitButton() {
            if (submitButton) {
                if (passwordValid && passwordsMatch) {
                    submitButton.disabled = false;
                    submitButton.className = 'btn btn-primary w-full';
                } else {
                    submitButton.disabled = true;
                    submitButton.className = 'btn btn-primary w-full opacity-50 cursor-not-allowed';
                }
            }
        }
    });

    // Password visibility toggle
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-toggle-icon');

        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
            }
        }
    }
</script>
@endpush
