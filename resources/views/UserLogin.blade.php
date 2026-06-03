@extends('layouts.auth')

@section('title')
    @if (isset($type))
        @switch($type)
            @case('public')
                Public User Login - MySebenarnya
            @break
            @case('mcmc')
                MCMC Staff Login - MySebenarnya
            @break
            @case('agency')
                Agency Login - MySebenarnya
            @break
            @default
                User Login - MySebenarnya
        @endswitch
    @else
        User Login - MySebenarnya
    @endif
@endsection

@section('page-title')
    @if (isset($type))
        @switch($type)
            @case('public')
                Public User Login
            @break
            @case('mcmc')
                MCMC Staff Login
            @break
            @case('agency')
                Agency Login
            @break
            @default
                User Login
        @endswitch
    @else
        User Login
    @endif
@endsection

@section('page-subtitle', 'Sign in to access your account')

@section('content')
    <!-- Login Type Selector (if no type specified) -->
    @if (!isset($type))
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Choose Login Type</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('login', ['type' => 'public']) }}"
                   class="login-type-btn flex items-center justify-center p-4 border-2 rounded-lg hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                   aria-label="Login as Public User">
                    <div class="flex items-center">
                        <i class="fas fa-user text-blue-600 text-xl mr-3" aria-hidden="true"></i>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Public User</div>
                            <div class="text-sm text-gray-500">General public access</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('login', ['type' => 'mcmc']) }}"
                   class="login-type-btn flex items-center justify-center p-4 border-2 rounded-lg hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                   aria-label="Login as MCMC Staff">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-purple-600 text-xl mr-3" aria-hidden="true"></i>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">MCMC Staff</div>
                            <div class="text-sm text-gray-500">Staff member access</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('login', ['type' => 'agency']) }}"
                   class="login-type-btn flex items-center justify-center p-4 border-2 rounded-lg hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                   aria-label="Login as Agency">
                    <div class="flex items-center">
                        <i class="fas fa-building text-green-600 text-xl mr-3" aria-hidden="true"></i>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Agency</div>
                            <div class="text-sm text-gray-500">Agency representative access</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @else
        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

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
                    We'll never share your email with anyone else.
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
                           placeholder="Enter your password"
                           required
                           autocomplete="current-password"
                           aria-describedby="@error('password') password-error @enderror">
                    <button type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            onclick="togglePassword()"
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
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox"
                           id="remember"
                           name="remember"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors duration-200">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="/forgot-password"
                       class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200 focus:outline-none focus:underline">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="btn btn-primary w-full"
                        aria-describedby="login-help">
                    <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                    Sign In
                    <span class="loading-spinner ml-2 hidden" id="login-spinner" aria-hidden="true"></span>
                </button>
                <div id="login-help" class="form-help text-center mt-2">
                    By signing in, you agree to our terms of service and privacy policy.
                </div>
            </div>
        </form>
    @endif
@endsection

@section('additional-links')
    @if (isset($type))
        <div class="space-y-3">
            @if ($type === 'public')
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}"
                       class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200 focus:outline-none focus:underline">
                        Register here
                    </a>
                </p>
            @endif

            <p class="text-sm text-gray-600">
                Need a different login type?
                <a href="{{ route('login') }}"
                   class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200 focus:outline-none focus:underline">
                    Choose login type
                </a>
            </p>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('password-toggle-icon');

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

    // Form submission with loading state
    document.querySelector('form')?.addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        const spinner = document.getElementById('login-spinner');

        if (submitButton && spinner) {
            submitButton.disabled = true;
            spinner.classList.remove('hidden');
            submitButton.querySelector('span:not(.loading-spinner)').textContent = 'Signing In...';
        }
    });

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.classList.contains('login-type-btn')) {
            e.target.click();
        }
    });
</script>
@endpush
