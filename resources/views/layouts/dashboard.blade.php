@extends('layouts.app')

@section('navigation')
    <nav class="bg-white shadow-sm border-b border-gray-200" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="h-8 w-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gradient">MySebenarnya</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-6">
                        @yield('nav-links')
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button type="button"
                                class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors duration-200"
                                aria-label="View notifications"
                                onclick="toggleNotifications()">
                            <i class="fas fa-bell text-lg" aria-hidden="true"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown"
                             class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                             role="menu"
                             aria-labelledby="notifications-button">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <div class="p-4 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-500" aria-hidden="true"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm text-gray-900">Welcome to MySebenarnya!</p>
                                            <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-500">View all notifications</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button type="button"
                                class="flex items-center text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                aria-label="User menu"
                                onclick="toggleUserMenu()">
                            <div class="flex items-center space-x-3 px-3 py-2 hover:bg-gray-50 rounded-lg">
                                @if(Auth::user()->profile_picture)
                                    <img class="h-8 w-8 rounded-full object-cover"
                                         src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                         alt="{{ Auth::user()->name }}'s profile picture">
                                @else
                                    <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 capitalize">{{ Auth::user()->user_type }}</div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs" aria-hidden="true"></i>
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div id="user-dropdown"
                             class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                             role="menu"
                             aria-labelledby="user-menu-button">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    @if(Auth::user()->profile_picture)
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                             alt="{{ Auth::user()->name }}'s profile picture">
                                    @else
                                        <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="py-2">
                                @yield('user-menu-items')

                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                                role="menuitem">
                                            <i class="fas fa-sign-out-alt mr-3" aria-hidden="true"></i>
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button type="button"
                                class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg"
                                aria-label="Open mobile menu"
                                onclick="toggleMobileMenu()">
                            <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-1">
                @hasSection('mobile-nav-links')
                    @yield('mobile-nav-links')
                @else
                    @yield('nav-links')
                @endif
            </div>
        </div>
    </nav>
@endsection

@push('scripts')
<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notifications-dropdown');
        const userDropdown = document.getElementById('user-dropdown');

        // Close user dropdown if open
        userDropdown.classList.add('hidden');

        // Toggle notifications dropdown
        dropdown.classList.toggle('hidden');
    }

    function toggleUserMenu() {
        const dropdown = document.getElementById('user-dropdown');
        const notificationsDropdown = document.getElementById('notifications-dropdown');

        // Close notifications dropdown if open
        notificationsDropdown.classList.add('hidden');

        // Toggle user dropdown
        dropdown.classList.toggle('hidden');
    }

    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const userDropdown = document.getElementById('user-dropdown');
        const notificationsDropdown = document.getElementById('notifications-dropdown');
        const mobileMenu = document.getElementById('mobile-menu');

        // Check if click is outside dropdowns
        if (!event.target.closest('[onclick="toggleUserMenu()"]') &&
            !event.target.closest('#user-dropdown')) {
            userDropdown.classList.add('hidden');
        }

        if (!event.target.closest('[onclick="toggleNotifications()"]') &&
            !event.target.closest('#notifications-dropdown')) {
            notificationsDropdown.classList.add('hidden');
        }

        if (!event.target.closest('[onclick="toggleMobileMenu()"]') &&
            !event.target.closest('#mobile-menu')) {
            mobileMenu.classList.add('hidden');
        }
    });

    // Keyboard navigation for dropdowns
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('user-dropdown').classList.add('hidden');
            document.getElementById('notifications-dropdown').classList.add('hidden');
            document.getElementById('mobile-menu').classList.add('hidden');
        }
    });
</script>
@endpush
