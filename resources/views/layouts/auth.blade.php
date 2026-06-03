<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MySebenarnya - Authentication')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')

    <!-- Meta Description -->
    <meta name="description" content="@yield('description', 'Secure authentication for MySebenarnya digital services platform.')">

    <!-- Accessibility -->
    <meta name="theme-color" content="#4f46e5">
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 font-sans antialiased">
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50 transition-all duration-200">
        Skip to main content
    </a>

    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

    <!-- Main Container -->
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative">
        <!-- Logo and Branding -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shield-alt text-white text-2xl" aria-hidden="true"></i>
            </div>
            <h1 class="mt-6 text-3xl font-bold text-gray-900">
                @yield('page-title', 'MySebenarnya')
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                @yield('page-subtitle', 'Secure Digital Services Platform')
            </p>
        </div>

        <!-- Main Content -->
        <main id="main-content" class="sm:mx-auto sm:w-full sm:max-w-md" role="main">
            <!-- Flash Messages -->
            @if(session('success') || session('error') || session('warning') || session('info') || session('message'))
                <div class="mb-6">
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 fade-in" role="alert" aria-live="polite">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400" aria-hidden="true"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                                <button type="button" class="ml-auto flex-shrink-0 text-green-500 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 rounded-md p-1" onclick="this.parentElement.parentElement.remove()" aria-label="Dismiss notification">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 fade-in" role="alert" aria-live="assertive">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400" aria-hidden="true"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                                <button type="button" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md p-1" onclick="this.parentElement.parentElement.remove()" aria-label="Dismiss notification">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 fade-in" role="alert" aria-live="polite">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400" aria-hidden="true"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                                </div>
                                <button type="button" class="ml-auto flex-shrink-0 text-yellow-500 hover:text-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 rounded-md p-1" onclick="this.parentElement.parentElement.remove()" aria-label="Dismiss notification">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('info') || session('message'))
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 fade-in" role="alert" aria-live="polite">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400" aria-hidden="true"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800">{{ session('info') ?? session('message') }}</p>
                                </div>
                                <button type="button" class="ml-auto flex-shrink-0 text-blue-500 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-1" onclick="this.parentElement.parentElement.remove()" aria-label="Dismiss notification">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Auth Card -->
            <div class="bg-white py-8 px-6 shadow-xl rounded-xl border border-gray-100 card-shadow">
                @yield('content')
            </div>

            <!-- Additional Links -->
            <div class="mt-6 text-center">
                @yield('additional-links')

                <!-- Back to Home -->
                <div class="mt-4">
                    <a href="/" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
                        Back to Home
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-12 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} MySebenarnya. All rights reserved.
            </p>
        </footer>
    </div>

    <!-- Scripts -->
    @stack('scripts')

    <!-- Auto-dismiss flash messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss flash messages after 5 seconds
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            // Keyboard navigation for dismissible alerts
            const dismissButtons = document.querySelectorAll('[aria-label="Dismiss notification"]');
            dismissButtons.forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
    </script>

    <!-- Background Grid Pattern -->
    <style>
        .bg-grid-pattern {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</body>
</html>
