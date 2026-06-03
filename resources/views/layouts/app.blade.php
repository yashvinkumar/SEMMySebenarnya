<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MySebenarnya - Digital Services Platform')</title>

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
    <meta name="description" content="@yield('description', 'MySebenarnya - Your trusted digital services platform for government and agency services.')">

    <!-- Accessibility -->
    <meta name="theme-color" content="#4f46e5">

    <!-- Preload critical resources -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" as="style">
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50 transition-all duration-200">
        Skip to main content
    </a>

    <!-- Page Wrapper -->
    <div class="min-h-full flex flex-col">
        <!-- Navigation -->
        @hasSection('navigation')
            @yield('navigation')
        @endif

        <!-- Main Content -->
        <main id="main-content" class="flex-1" role="main">
            <!-- Page Header -->
            @hasSection('header')
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Flash Messages -->
            @if(session('success') || session('error') || session('warning') || session('info') || session('message'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm fade-in" role="alert" aria-live="polite">
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
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md shadow-sm fade-in" role="alert" aria-live="assertive">
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
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm fade-in" role="alert" aria-live="polite">
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
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md shadow-sm fade-in" role="alert" aria-live="polite">
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

            <!-- Page Content -->
            <div class="@yield('content-class', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8')">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        @hasSection('footer')
            @yield('footer')
        @else
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">MySebenarnya</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Your trusted digital services platform for government and agency services.
                                Providing secure, accessible, and efficient online services for all citizens.
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Quick Links</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li><a href="/" class="hover:text-blue-600 transition-colors duration-200">Home</a></li>
                                <li><a href="/about" class="hover:text-blue-600 transition-colors duration-200">About</a></li>
                                <li><a href="/services" class="hover:text-blue-600 transition-colors duration-200">Services</a></li>
                                <li><a href="/contact" class="hover:text-blue-600 transition-colors duration-200">Contact</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-4">Support</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li><a href="/help" class="hover:text-blue-600 transition-colors duration-200">Help Center</a></li>
                                <li><a href="/privacy" class="hover:text-blue-600 transition-colors duration-200">Privacy Policy</a></li>
                                <li><a href="/terms" class="hover:text-blue-600 transition-colors duration-200">Terms of Service</a></li>
                                <li><a href="/accessibility" class="hover:text-blue-600 transition-colors duration-200">Accessibility</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 mt-8 pt-8 text-center">
                        <p class="text-sm text-gray-500">
                            © {{ date('Y') }} MySebenarnya. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        @endif
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
</body>
</html>
