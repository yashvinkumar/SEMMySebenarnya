@extends('layouts.auth')

@section('title', 'User Registration - MySebenarnya')
@section('page-title', 'Create Account')
@section('page-subtitle', 'Join MySebenarnya to access digital services')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-6" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- Full Name Field -->
        <div>
            <label for="name" class="form-label">
                <i class="fas fa-user mr-2 text-gray-400" aria-hidden="true"></i>
                Full Name
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-input @error('name') error @enderror"
                   placeholder="Enter your full name"
                   required
                   autocomplete="name"
                   aria-describedby="@error('name') name-error @enderror name-help">
            @error('name')
                <div id="name-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="name-help" class="form-help">
                Enter your full legal name as it appears on official documents.
            </div>
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="form-label">
                <i class="fas fa-envelope mr-2 text-gray-400" aria-hidden="true"></i>
                Email Address
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-input @error('email') error @enderror"
                   placeholder="Enter your email address"
                   required
                   autocomplete="email"
                   aria-describedby="@error('email') email-error @enderror email-help">
            @error('email')
                <div id="email-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="email-help" class="form-help">
                We'll use this email for account verification and important notifications.
            </div>
        </div>

        <!-- Contact Number Field -->
        <div>
            <label for="contact_number" class="form-label">
                <i class="fas fa-phone mr-2 text-gray-400" aria-hidden="true"></i>
                Contact Number
            </label>
            <input type="tel"
                   id="contact_number"
                   name="contact_number"
                   value="{{ old('contact_number') }}"
                   class="form-input @error('contact_number') error @enderror"
                   placeholder="Enter your contact number"
                   required
                   autocomplete="tel"
                   aria-describedby="@error('contact_number') contact-error @enderror contact-help">
            @error('contact_number')
                <div id="contact-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="contact-help" class="form-help">
                Include country code (e.g., +60123456789 for Malaysia).
            </div>
        </div>

        <!-- Profile Picture Field -->
        <div>
            <label for="profile_picture" class="form-label">
                <i class="fas fa-camera mr-2 text-gray-400" aria-hidden="true"></i>
                Profile Picture <span class="text-gray-500 text-sm">(Optional)</span>
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200">
                <div class="space-y-1 text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-cloud-upload-alt text-3xl" aria-hidden="true"></i>
                    </div>
                    <div class="flex text-sm text-gray-600">
                        <label for="profile_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload a file</span>
                            <input id="profile_picture"
                                   name="profile_picture"
                                   type="file"
                                   class="sr-only"
                                   accept="image/*"
                                   aria-describedby="@error('profile_picture') picture-error @enderror picture-help">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                </div>
            </div>
            @error('profile_picture')
                <div id="picture-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="picture-help" class="form-help">
                Upload a clear photo of yourself for your profile.
            </div>
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="form-label">
                <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                Password
            </label>
            <div class="relative">
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input @error('password') error @enderror pr-12"
                       placeholder="Create a strong password"
                       required
                       autocomplete="new-password"
                       aria-describedby="@error('password') password-error @enderror password-help">
                <button type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                        onclick="togglePassword('password')"
                        aria-label="Toggle password visibility">
                    <i id="password-toggle-icon" class="fas fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <div id="password-error" class="form-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                    {{ $message }}
                </div>
            @enderror
            <div id="password-help" class="form-help">
                Password must be at least 8 characters with uppercase, lowercase, number, and special character.
            </div>

            <!-- Password Strength Indicator -->
            <div class="mt-2">
                <div class="flex space-x-1" id="password-strength">
                    <div class="h-1 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-1 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-1 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-1 w-1/4 bg-gray-200 rounded"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1" id="password-strength-text">Password strength</p>
            </div>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock mr-2 text-gray-400" aria-hidden="true"></i>
                Confirm Password
            </label>
            <div class="relative">
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input pr-12"
                       placeholder="Confirm your password"
                       required
                       autocomplete="new-password"
                       aria-describedby="password-match-help">
                <button type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                        onclick="togglePassword('password_confirmation')"
                        aria-label="Toggle password confirmation visibility">
                    <i id="password_confirmation-toggle-icon" class="fas fa-eye" aria-hidden="true"></i>
                </button>
            </div>
            <div id="password-match-help" class="form-help">
                <span id="password-match-text">Re-enter your password to confirm.</span>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="terms"
                       name="terms"
                       type="checkbox"
                       required
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors duration-200"
                       aria-describedby="terms-help">
            </div>
            <div class="ml-3 text-sm">
                <label for="terms" class="text-gray-700">
                    I agree to the
                    <a href="/terms" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200" target="_blank">
                        Terms of Service
                    </a>
                    and
                    <a href="/privacy" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200" target="_blank">
                        Privacy Policy
                    </a>
                </label>
                <div id="terms-help" class="form-help mt-1">
                    Please read and accept our terms to continue.
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="btn btn-primary w-full"
                    id="register-btn"
                    aria-describedby="register-help">
                <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                Create Account
                <span class="loading-spinner ml-2 hidden" id="register-spinner" aria-hidden="true"></span>
            </button>
            <div id="register-help" class="form-help text-center mt-2">
                By creating an account, you'll receive a verification email.
            </div>
        </div>
    </form>
@endsection

@section('additional-links')
    <div class="text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login', ['type' => 'public']) }}"
               class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200 focus:outline-none focus:underline">
                Sign in here
            </a>
        </p>
    </div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(fieldId + '-toggle-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        const checks = [
            /.{8,}/, // At least 8 characters
            /[a-z]/, // Lowercase
            /[A-Z]/, // Uppercase
            /[0-9]/, // Numbers
            /[^A-Za-z0-9]/ // Special characters
        ];

        checks.forEach(check => {
            if (check.test(password)) strength++;
        });

        return Math.min(strength, 4);
    }

    // Update password strength indicator
    function updatePasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBars = document.querySelectorAll('#password-strength div');
        const strengthText = document.getElementById('password-strength-text');
        const strength = checkPasswordStrength(password);

        const colors = ['bg-gray-200', 'bg-red-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-400'];
        const texts = ['Password strength', 'Very weak', 'Weak', 'Good', 'Strong'];

        strengthBars.forEach((bar, index) => {
            bar.className = `h-1 w-1/4 rounded ${index < strength ? colors[strength] : 'bg-gray-200'}`;
        });

        strengthText.textContent = texts[strength];
        strengthText.className = `text-xs mt-1 ${strength > 2 ? 'text-green-600' : strength > 1 ? 'text-yellow-600' : 'text-red-600'}`;
    }

    // Password confirmation checker
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const matchText = document.getElementById('password-match-text');

        if (confirmation === '') {
            matchText.textContent = 'Re-enter your password to confirm.';
            matchText.className = 'text-gray-500';
        } else if (password === confirmation) {
            matchText.textContent = 'Passwords match!';
            matchText.className = 'text-green-600';
        } else {
            matchText.textContent = 'Passwords do not match.';
            matchText.className = 'text-red-600';
        }
    }

    // File upload preview
    function handleFileUpload() {
        const fileInput = document.getElementById('profile_picture');
        const uploadArea = fileInput.closest('.border-dashed');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadArea.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" alt="Profile preview" class="mx-auto h-20 w-20 rounded-full object-cover">
                            <p class="mt-2 text-sm text-gray-600">${file.name}</p>
                            <button type="button" onclick="clearFileUpload()" class="mt-1 text-xs text-red-600 hover:text-red-800">Remove</button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function clearFileUpload() {
        const fileInput = document.getElementById('profile_picture');
        const uploadArea = fileInput.closest('.border-dashed');
        fileInput.value = '';

        uploadArea.innerHTML = `
            <div class="space-y-1 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-cloud-upload-alt text-3xl" aria-hidden="true"></i>
                </div>
                <div class="flex text-sm text-gray-600">
                    <label for="profile_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                        <span>Upload a file</span>
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
            </div>
        `;
        handleFileUpload();
    }

    // Form submission with loading state
    document.querySelector('form').addEventListener('submit', function() {
        const submitButton = document.getElementById('register-btn');
        const spinner = document.getElementById('register-spinner');

        submitButton.disabled = true;
        spinner.classList.remove('hidden');
        submitButton.querySelector('span:not(.loading-spinner)').textContent = 'Creating Account...';
    });

    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('password').addEventListener('input', updatePasswordStrength);
        document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);
        document.getElementById('password').addEventListener('input', checkPasswordMatch);
        handleFileUpload();
    });
</script>
@endpush
