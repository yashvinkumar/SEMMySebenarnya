@extends('layouts.dashboard')

@section('title', 'User Profile - MySebenarnya')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-user mr-3 text-blue-600" aria-hidden="true"></i>
                Profile Settings
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Manage your personal information and account settings
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ url()->previous() }}"
               class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                Back
            </a>
        </div>
    </div>
@endsection

@section('user-menu-items')
    @if(Auth::user()->user_type == 'public')
        <a href="{{ route('public.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-user mr-3" aria-hidden="true"></i>
            Profile
        </a>
        <a href="{{ route('public.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-cog mr-3" aria-hidden="true"></i>
            Settings
        </a>
    @elseif(Auth::user()->user_type == 'mcmc')
        <a href="{{ route('mcmc.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-user mr-3" aria-hidden="true"></i>
            Profile
        </a>
        <a href="{{ route('mcmc.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-cog mr-3" aria-hidden="true"></i>
            Settings
        </a>
    @elseif(Auth::user()->user_type == 'agency')
        <a href="{{ route('agency.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-user mr-3" aria-hidden="true"></i>
            Profile
        </a>
        <a href="{{ route('agency.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
            <i class="fas fa-cog mr-3" aria-hidden="true"></i>
            Settings
        </a>
    @endif
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-8 border border-blue-100">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if(Auth::user()->profile_picture)
                        <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg"
                             src="{{ Storage::url(Auth::user()->profile_picture) }}"
                             alt="{{ Auth::user()->name }}'s profile picture">
                    @else
                        <div class="h-24 w-24 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                            <span class="text-white text-2xl font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-2">
                        <i class="fas fa-check text-white text-sm" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <div class="flex items-center mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                            <i class="fas fa-user-tag mr-1" aria-hidden="true"></i>
                            {{ Auth::user()->user_type }} User
                        </span>
                        @if(Auth::user()->email_verified_at)
                            <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Profile Information -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-edit mr-2 text-blue-600" aria-hidden="true"></i>
                            Personal Information
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Update your personal details and contact information</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route(Auth::user()->user_type . '.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Profile Picture Upload -->
                            <div>
                                <label class="form-label">
                                    <i class="fas fa-camera mr-2 text-gray-400" aria-hidden="true"></i>
                                    Profile Picture
                                </label>
                                <div class="mt-1 flex items-center space-x-4">
                                    @if(Auth::user()->profile_picture)
                                        <img class="h-16 w-16 rounded-full object-cover"
                                             src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                             alt="Current profile picture">
                                    @else
                                        <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xl font-bold">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <input type="file"
                                               id="profile_picture"
                                               name="profile_picture"
                                               accept="image/*"
                                               class="form-input"
                                               aria-describedby="picture-help">
                                        <div id="picture-help" class="form-help">
                                            Upload a new profile picture (JPG, PNG, GIF up to 2MB)
                                        </div>
                                    </div>
                                </div>
                                @error('profile_picture')
                                    <div class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Name and Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user mr-2 text-gray-400" aria-hidden="true"></i>
                                        Full Name
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', Auth::user()->name) }}"
                                           class="form-input @error('name') error @enderror"
                                           required
                                           autocomplete="name"
                                           aria-describedby="@error('name') name-error @enderror">
                                    @error('name')
                                        <div id="name-error" class="form-error" role="alert">
                                            <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope mr-2 text-gray-400" aria-hidden="true"></i>
                                        Email Address
                                    </label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', Auth::user()->email) }}"
                                           class="form-input @error('email') error @enderror"
                                           required
                                           autocomplete="email"
                                           aria-describedby="@error('email') email-error @enderror">
                                    @error('email')
                                        <div id="email-error" class="form-error" role="alert">
                                            <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label for="contact_number" class="form-label">
                                    <i class="fas fa-phone mr-2 text-gray-400" aria-hidden="true"></i>
                                    Contact Number
                                </label>
                                <input type="tel"
                                       id="contact_number"
                                       name="contact_number"
                                       value="{{ old('contact_number', Auth::user()->contact_number) }}"
                                       class="form-input @error('contact_number') error @enderror"
                                       required
                                       autocomplete="tel"
                                       aria-describedby="@error('contact_number') contact-error @enderror">
                                @error('contact_number')
                                    <div id="contact-error" class="form-error" role="alert">
                                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Change Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-lock mr-2 text-gray-600" aria-hidden="true"></i>
                                    Change Password
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change your password</p>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <div class="relative">
                                            <input type="password"
                                                   id="current_password"
                                                   name="current_password"
                                                   class="form-input @error('current_password') error @enderror pr-12"
                                                   autocomplete="current-password"
                                                   aria-describedby="@error('current_password') current-password-error @enderror">
                                            <button type="button"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                                    onclick="togglePassword('current_password')"
                                                    aria-label="Toggle current password visibility">
                                                <i id="current_password-toggle-icon" class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div id="current-password-error" class="form-error" role="alert">
                                                <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="relative">
                                            <input type="password"
                                                   id="password"
                                                   name="password"
                                                   class="form-input @error('password') error @enderror pr-12"
                                                   autocomplete="new-password"
                                                   aria-describedby="@error('password') password-error @enderror">
                                            <button type="button"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                                    onclick="togglePassword('password')"
                                                    aria-label="Toggle new password visibility">
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

                                    <div>
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <div class="relative">
                                            <input type="password"
                                                   id="password_confirmation"
                                                   name="password_confirmation"
                                                   class="form-input pr-12"
                                                   autocomplete="new-password">
                                            <button type="button"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                                    onclick="togglePassword('password_confirmation')"
                                                    aria-label="Toggle password confirmation visibility">
                                                <i id="password_confirmation-toggle-icon" class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4 pt-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2" aria-hidden="true"></i>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Information Sidebar -->
            <div class="lg:col-span-1">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-info-circle mr-2 text-blue-600" aria-hidden="true"></i>
                            Account Information
                        </h3>
                    </div>

                    <div class="card-body space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Account Type</span>
                            <span class="text-sm text-gray-900 capitalize">{{ Auth::user()->user_type }}</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Member Since</span>
                            <span class="text-sm text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Email Status</span>
                            @if(Auth::user()->email_verified_at)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                    Unverified
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-medium text-gray-600">Last Updated</span>
                            <span class="text-sm text-gray-900">{{ Auth::user()->updated_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-6">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-bolt mr-2 text-yellow-600" aria-hidden="true"></i>
                            Quick Actions
                        </h3>
                    </div>

                    <div class="card-body space-y-3">
                        @if(!Auth::user()->email_verified_at)
                            <button type="button" class="btn btn-warning w-full text-sm">
                                <i class="fas fa-envelope mr-2" aria-hidden="true"></i>
                                Resend Verification Email
                            </button>
                        @endif

                        <a href="{{ route(Auth::user()->user_type . '.settings') }}" class="btn btn-secondary w-full text-sm">
                            <i class="fas fa-cog mr-2" aria-hidden="true"></i>
                            Account Settings
                        </a>

                        <a href="{{ route(Auth::user()->user_type . '.dashboard') }}" class="btn btn-info w-full text-sm">
                            <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
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

    // Profile picture preview
    document.getElementById('profile_picture')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update all profile pictures on the page
                const profileImages = document.querySelectorAll('img[alt*="profile picture"], img[alt="Current profile picture"]');
                profileImages.forEach(img => {
                    img.src = e.target.result;
                });
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
