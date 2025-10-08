<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','CleanSaver Naga - Professional Cleaning Services')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gray-100 font-sans flex flex-col">
    {{-- Simplified Navigation for Landing Page --}}
    <nav class="bg-brand-green fixed top-0 left-0 right-0 z-50 shadow-lg backdrop-blur-sm">
        <div class="max-w-7xl mx-auto h-16 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex gap-4">
                <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="CleanSaver Naga Logo" class="h-12">
            </div>
    
            <!-- Simplified Nav Links - Overview, Services, About Us and Sign In/Up -->
            <div class="flex items-center gap-4">
                <a href="#about-us" class="border rounded-full border-white px-3 py-2 text-white hover:bg-white hover:text-emerald-700 transition-colors">Overview</a>
                <a href="{{ route('services') }}" class="border rounded-full border-white px-3 py-2 text-white hover:bg-white hover:text-emerald-700 transition-colors">Services</a>
                <a href="#about-us" class="border rounded-full border-white px-3 py-2 text-white hover:bg-white hover:text-emerald-700 transition-colors">About Us</a>
                
                {{-- Sign In Button --}}
                <a href="{{ route('login') }}" class="border rounded-full border-white px-3 py-2 text-white hover:bg-white hover:text-emerald-700 transition-colors">
                    Sign In
                </a>
                
                {{-- Sign Up Button --}}
                <a href="{{ route('register') }}" class="bg-white text-emerald-700 font-semibold px-4 py-2 rounded-full hover:bg-gray-100 transition-colors">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-brand-green text-white border-t rounded-t-3xl">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Main Footer Content -->
            <div class="mb-6">
                
                <!-- Company Info -->
                <div class="max-w-2xl mx-auto text-center">
                    <h3 class="text-lg font-bold text-white mb-3">CleanSaver Naga</h3>
                    <p class="text-sm opacity-90 leading-relaxed mb-3">
                        Professional cleaning services in Naga City. We provide comprehensive cleaning solutions for homes, offices, and commercial spaces.
                    </p>
                    <div class="flex items-center justify-center gap-2 text-sm opacity-90">
                        <i class="ri-map-pin-fill text-green-300"></i>
                        <span>0772 Maescoy Compound, San Felipe Naga City</span>
                    </div>
                </div>

            <!-- Social Media & Contact -->
            <div class="border-t border-white/20 pt-6 mb-6">
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3">
                    <a href="https://www.facebook.com/cleansavernaga" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="group flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="ri-facebook-fill text-lg text-white"></i>
                        <span class="text-sm font-medium">@cleansavernaga</span>
                    </a>
                    
                    <a href="https://www.instagram.com/cleansavernaga_ph" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="group flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="ri-instagram-fill text-lg text-white"></i>
                        <span class="text-sm font-medium">@cleansavernaga_ph</span>
                    </a>
                    
                    <a href="mailto:cleansaverph.naga@gmail.com" 
                       class="group flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="ri-mail-fill text-lg text-white"></i>
                        <span class="text-sm font-medium">cleansaverph.naga@gmail.com</span>
                    </a>
                    
                    <div class="flex items-center gap-2 px-3 py-2 bg-white/10 rounded-lg">
                        <i class="ri-phone-fill text-lg text-white"></i>
                        <span class="text-sm font-medium">(+63) 995 112 0443</span>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="text-center border-t border-white/20 pt-4">
                <p class="text-sm opacity-75">
                    &copy; {{ date('Y') }} CleanSaver Naga. All rights reserved. | 
                    <a href="/privacy-policy" class="hover:text-white transition-colors duration-200">Privacy Policy</a> | 
                    <a href="/terms-of-service" class="hover:text-white transition-colors duration-200">Terms of Service</a> |
                    <a href="/sitemap.xml" class="hover:text-white transition-colors duration-200">Sitemap</a>
                </p>
            </div>
        </div>
    </footer>


    {{-- Smooth scrolling script for About Us link --}}
    <script>
        // Smooth scrolling for About Us link
        document.addEventListener('DOMContentLoaded', function() {
            const aboutUsLink = document.querySelector('a[href="#about-us"]');
            if (aboutUsLink) {
                aboutUsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const aboutSection = document.getElementById('about-us');
                    if (aboutSection) {
                        aboutSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }
        });
    </script>
</body>
@stack('scripts')
</html>
