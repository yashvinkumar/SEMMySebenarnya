<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MySebenarnya - Secure platform for Malaysian Communications and Multimedia Commission services">
    <title>{{ config('app.name', 'MySebenarnya') }} - Welcome</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="MySebenarnya Logo" class="h-10 w-10 mr-3">
                        <h1 class="text-2xl font-bold text-teal-600">
                            MySebenarnya
                        </h1>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#features" class="text-gray-600 hover:text-teal-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Features
                        </a>
                        <a href="#about" class="text-gray-600 hover:text-teal-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            About
                        </a>
                        <a href="#contact" class="text-gray-600 hover:text-teal-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Contact
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button"
                            class="mobile-menu-button bg-gray-100 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                            aria-controls="mobile-menu"
                            aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                <a href="#features" class="text-gray-600 hover:text-teal-600 block px-3 py-2 rounded-md text-base font-medium">Features</a>
                <a href="#about" class="text-gray-600 hover:text-teal-600 block px-3 py-2 rounded-md text-base font-medium">About</a>
                <a href="#contact" class="text-gray-600 hover:text-teal-600 block px-3 py-2 rounded-md text-base font-medium">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-teal-500 via-cyan-600 to-blue-700 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-40 right-20 w-16 h-16 bg-white bg-opacity-10 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-40 left-20 w-12 h-12 bg-white bg-opacity-10 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-24 h-24 bg-white bg-opacity-10 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <!-- MCMC Logo/Badge -->
                <div class="mb-8 animate-fade-in-up">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-4 animate-float">
                        <i class="fas fa-broadcast-tower text-3xl text-white" aria-hidden="true"></i>
                    </div>
                    <p class="text-teal-100 text-sm font-medium">Malaysian Communications and Multimedia Commission</p>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight animate-fade-in-up text-white" style="animation-delay: 0.2s;">
                    Welcome to
                    <span class="text-yellow-300">MySebenarnya</span>
                </h1>

                <p class="text-xl md:text-2xl text-teal-100 mb-8 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.4s;">
                    Your trusted digital gateway for secure access to MCMC services.
                    Experience seamless, secure, and efficient government services.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in-up" style="animation-delay: 0.6s;">
                    <a href="#access-types"
                       class="btn btn-primary btn-lg px-8 py-4 text-lg font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-rocket mr-2" aria-hidden="true"></i>
                        Get Started
                    </a>
                    <a href="#features"
                       class="btn btn-outline btn-lg px-8 py-4 text-lg font-semibold rounded-lg border-2 border-white text-white hover:bg-white hover:text-gray-800 transition-all duration-200">
                        <i class="fas fa-info-circle mr-2" aria-hidden="true"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#access-types" class="inline-flex items-center justify-center w-12 h-12 glass rounded-full text-white hover:text-yellow-300 transition-all duration-300 hover:scale-110">
                <i class="fas fa-chevron-down text-xl" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <!-- Access Types Section -->
    <section id="access-types" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Choose Your Access Type
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Select the appropriate portal based on your role and access requirements
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Public User -->
                <div class="group animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="card hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 h-full hover-lift hover-glow">
                        <div class="card-body text-center p-8">
                            <!-- Icon -->
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-users text-2xl text-white" aria-hidden="true"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Public User</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Access public services, submit applications, and manage your personal account with ease.
                            </p>

                            <!-- Features List -->
                            <ul class="text-sm text-gray-500 mb-8 space-y-2">
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Service Applications
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Account Management
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Status Tracking
                                </li>
                            </ul>

                            <!-- Actions -->
                            <div class="space-y-3">
                                <a href="{{ route('login') }}?type=public"
                                   class="btn btn-primary w-full">
                                    <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                                    Login
                                </a>
                                <a href="{{ route('register') }}"
                                   class="btn btn-outline w-full">
                                    <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                                    Register New Account
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MCMC Staff -->
                <div class="group animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="card hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 h-full border-2 border-green-200 hover-lift hover-glow">
                        <div class="card-body text-center p-8">
                            <!-- Featured Badge >
                            <div-- class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                <span class="bg-green-500 text-white px-4 py-1 rounded-full text-xs font-semibold">
                                    STAFF PORTAL
                                </span>
                            </div-->

                            <!-- Icon -->
                            <div class="mb-6 mt-4">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-user-tie text-2xl text-white" aria-hidden="true"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">MCMC Staff</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Administrative access for MCMC staff members to manage users, agencies, and generate reports.
                            </p>

                            <!-- Features List -->
                            <ul class="text-sm text-gray-500 mb-8 space-y-2">
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    User Management
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Agency Registration
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Reports & Analytics
                                </li>
                            </ul>

                            <!-- Actions -->
                            <div class="space-y-3">
                                <a href="{{ route('login') }}?type=mcmc"
                                   class="btn btn-success w-full">
                                    <i class="fas fa-shield-alt mr-2" aria-hidden="true"></i>
                                    Staff Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agency -->
                <div class="group animate-fade-in-up" style="animation-delay: 0.5s;">
                    <div class="card hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 h-full hover-lift hover-glow">
                        <div class="card-body text-center p-8">
                            <!-- Icon -->
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-building text-2xl text-white" aria-hidden="true"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Agency</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Dedicated access portal for registered agencies and organizations to manage their services.
                            </p>

                            <!-- Features List -->
                            <ul class="text-sm text-gray-500 mb-8 space-y-2">
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Agency Dashboard
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Service Management
                                </li>
                                <li class="flex items-center justify-center">
                                    <i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>
                                    Client Applications
                                </li>
                            </ul>

                            <!-- Actions -->
                            <div class="space-y-3">
                                <a href="{{ route('login') }}?type=agency"
                                   class="btn btn-purple w-full">
                                    <i class="fas fa-building mr-2" aria-hidden="true"></i>
                                    Agency Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Why Choose MySebenarnya?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Experience the next generation of government digital services with enhanced security and user experience
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Security -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-alt text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Advanced Security</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Multi-layered security protocols, encryption, and secure authentication to protect your sensitive data and privacy.
                    </p>
                </div>

                <!-- Availability -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-clock text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">24/7 Availability</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Access your services anytime, anywhere with our reliable, high-performance platform designed for continuous operation.
                    </p>
                </div>

                <!-- Support -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Expert Support</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dedicated support team ready to assist you with comprehensive help documentation and responsive customer service.
                    </p>
                </div>

                <!-- User Experience -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-400 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-mobile-alt text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Mobile Responsive</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Optimized for all devices with intuitive design that works seamlessly on desktop, tablet, and mobile platforms.
                    </p>
                </div>

                <!-- Efficiency -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bolt text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Fast & Efficient</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Streamlined processes and optimized performance ensure quick service delivery and minimal waiting times.
                    </p>
                </div>

                <!-- Compliance -->
                <div class="text-center group">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-certificate text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Compliance Ready</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Built to meet government standards and regulatory requirements with full audit trails and documentation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        About MySebenarnya
                    </h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        MySebenarnya is the official digital platform of the Malaysian Communications and Multimedia Commission (MCMC),
                        designed to provide secure, efficient, and user-friendly access to government services.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Our platform serves as a comprehensive gateway for citizens, agencies, and MCMC staff to interact with
                        various telecommunications and multimedia services, ensuring transparency, security, and accessibility.
                    </p>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-teal-600 mb-2">10K+</div>
                            <div class="text-sm text-gray-600">Active Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">99.9%</div>
                            <div class="text-sm text-gray-600">Uptime</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-2">500+</div>
                            <div class="text-sm text-gray-600">Agencies</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                            <div class="text-sm text-gray-600">Support</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                        <div class="text-center">
                            <i class="fas fa-broadcast-tower text-6xl mb-6 opacity-80" aria-hidden="true"></i>
                            <h3 class="text-2xl font-bold mb-4">MCMC Digital Services</h3>
                            <p class="text-blue-100 leading-relaxed">
                                Empowering Malaysia's digital future through innovative telecommunications and multimedia solutions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Need Help?
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Our support team is here to assist you with any questions or technical issues
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Email Support -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-6">
                        <i class="fas fa-envelope text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Email Support</h3>
                    <p class="text-gray-300 mb-4">Get help via email for non-urgent inquiries</p>
                    <a href="mailto:support@mcmc.gov.my"
                       class="text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                        support@mcmc.gov.my
                    </a>
                </div>

                <!-- Phone Support -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-6">
                        <i class="fas fa-phone text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Phone Support</h3>
                    <p class="text-gray-300 mb-4">Speak directly with our support team</p>
                    <a href="tel:+60312345678"
                       class="text-green-400 hover:text-green-300 font-medium transition-colors duration-200">
                        +60 3-1234-5678
                    </a>
                </div>

                <!-- Live Chat -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-full mb-6">
                        <i class="fas fa-comments text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Live Chat</h3>
                    <p class="text-gray-300 mb-4">Chat with us for immediate assistance</p>
                    <button class="text-purple-400 hover:text-purple-300 font-medium transition-colors duration-200">
                        Start Chat
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and Description -->
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4">
                        <i class="fas fa-shield-alt mr-2 text-blue-400" aria-hidden="true"></i>
                        MySebenarnya
                    </h3>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                        The official digital platform of the Malaysian Communications and Multimedia Commission (MCMC),
                        providing secure and efficient access to government services.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">System Status</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-400" aria-hidden="true"></i>
                            Kuala Lumpur, Malaysia
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-blue-400" aria-hidden="true"></i>
                            +60 3-1234-5678
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-400" aria-hidden="true"></i>
                            support@mcmc.gov.my
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Malaysian Communications and Multimedia Commission (MCMC). All rights reserved.
                </p>
                <p class="text-gray-400 text-sm mt-2 md:mt-0">
                    Powered by Laravel & Tailwind CSS
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add scroll effect to navigation
            window.addEventListener('scroll', function() {
                const nav = document.querySelector('nav');
                if (window.scrollY > 100) {
                    nav.classList.add('shadow-lg');
                } else {
                    nav.classList.remove('shadow-lg');
                }
            });
        });
    </script>
</body>
</html>
