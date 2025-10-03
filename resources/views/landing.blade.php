@extends('layouts.landing')

@section('title','CleanSaver Naga - Professional Cleaning Services')

@section('content')
<div class="max-w-7xl mx-auto pt-7">
    {{-- Hero Section --}}
    <section class="relative w-screen -mx-[calc(50vw-50%)] rounded-2xl overflow-hidden mt-16">
        <img id="heroImage" src="{{ asset('assets/cs-dashborard-req-2.webp') }}" alt="Fresh Spaces, Happy Faces" class="w-full h-[600px] md:h-[500px] object-cover transition-opacity duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"></div>
        <div class="absolute inset-0 flex flex-col justify-end px-6 md:px-10 pb-8 gap-4">
            <h1 class="text-white text-2xl md:text-4xl font-extrabold max-w-2xl">Fresh Spaces, Happy Faces</h1>
            <div>
                <a href="{{ route('login') }}" class="inline-block bg-brand-green text-white font-semibold px-5 py-3 rounded-lg shadow hover:bg-emerald-700">Get Started Today</a>
            </div>
        </div>
    </section>

    {{-- Our Services --}}
    <section class="mt-10">
        <h2 class="text-xl md:text-2xl font-extrabold text-emerald-900">Our Services</h2>
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-8 gap-4">
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-carpet-cleaning.webp') }}" alt="Carpet Deep Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Carpet Deep Cleaning</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-home-dis.webp') }}" alt="Enhanced Disinfection" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Home/Office Disinfection</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-sofa-mattress-cleaning.webp') }}" alt="Sofa / Mattress Deep Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Sofa / Mattress Deep Cleaning</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-car-detailing.webp') }}" alt="Car Interior Detailing" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Car Interior Detailing</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-glass-cleaning.webp') }}" alt="Glass Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Glass Cleaning</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-post-cons-cleaning.webp') }}" alt="Post Construction Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Post Construction Cleaning</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/home-cleaning.webp') }}" alt="House Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">House Cleaning</div>
            </a>
            <a href="{{ route('login') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out cursor-pointer">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/curtain-cleaning.webp') }}" alt="Curtain Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Curtain Cleaning</div>
            </a>
        </div>
    </section>

    {{-- Hero Banner --}}
    <section class="relative w-screen -mx-[calc(50vw-50%)] bg-emerald-700 mt-10">
        <div class="max-w-7xl mx-auto py-3 relative">
            <img src="{{ asset('assets/cs-dashborard-req-1.webp') }}" alt="Cleansaver Naga" class="w-full h-[300px] md:h-[360px] object-cover object-top rounded-xl shadow-md">
            <div class="absolute inset-0">
                <div class="pt-6 pl-6 md:pt-8 md:pl-10">
                    <p class="text-white/90 text-sm md:text-base font-semibold">Experience the joy of spotless living with</p>
                    <h3 class="text-white text-3xl md:text-5xl font-extrabold mt-2">Cleansaver Naga</h3>
                </div>
                <div class="absolute right-4 bottom-3 md:right-10 md:bottom-6">
                    <a href="{{ route('login') }}" class="inline-block bg-white text-gray-900 font-semibold px-5 py-2 rounded-full shadow hover:bg-gray-100">Get Started Today</a>
                </div>
            </div>
        </div>
    </section>
    
{{-- About Us --}}
<section id="about-us" class="relative w-screen -mx-[calc(50vw-50%)] overflow-hidden">
    <!-- Background image -->
    <img src="{{ asset('assets/cs-dashboard-about.webp') }}" 
         alt="Fresh Spaces, Happy Faces" 
         class="w-full h-[400px] md:h-[500px] object-cover object-center opacity-25">

    <!-- Content -->
    <div class="absolute inset-0 flex flex-col justify-center items-start px-6 md:px-16 gap-4 text-left">
        
        <!-- About Us small label -->
        <span class="text-emerald-600 font-semibold uppercase tracking-wide text-sm">
            About Us
        </span>

        <!-- Heading -->
        <h1 class="text-3xl md:text-5xl font-extrabold text-black max-w-3xl leading-snug">
            Fresh Spaces, Happy Faces
        </h1>

        <!-- Paragraph -->
        <p class="text-gray-700 max-w-2xl">
            Cleansaver Naga is your trusted cleaning service provider in Naga City, Camarines Sur. 
            We take pride in helping homes and businesses achieve spotless spaces for a healthier 
            and more comfortable environment.
        </p>

        <p class="text-gray-700 max-w-2xl">
            Our dedicated team specializes in thorough cleaning methods, trusted and appreciated by 
            our growing number of satisfied clients.
        </p>

        <!-- Tagline -->
        <p class="italic font-semibold text-gray-800 max-w-2xl">
            Choose Cleansaver Naga for the best results at the best value because you deserve a 
            cleaner tomorrow, today.
        </p>

        <!-- Call to Action -->
        <div class="mt-6">
            <a href="{{ route('register') }}" class="inline-block bg-brand-green text-white font-semibold px-6 py-3 rounded-lg shadow hover:bg-emerald-700 transition-colors">
                Join Our Community Today
            </a>
        </div>
    </div>
</section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const images = [
        "{{ asset('assets/cs-dashborard-req-1.webp') }}",
        "{{ asset('assets/cs-dashborard-req-2.webp') }}",
        "{{ asset('assets/cs-dashboard-home-dis.webp') }}",
        "{{ asset('assets/cs-dashboard-glass-cleaning.webp') }}"
    ];
    const imgEl = document.getElementById('heroImage');
    if (!imgEl || images.length < 2) return;
    let idx = 0;
    setInterval(() => {
        idx = (idx + 1) % images.length;
        imgEl.classList.add('opacity-0');
        setTimeout(() => {
            imgEl.src = images[idx];
            imgEl.onload = () => imgEl.classList.remove('opacity-0');
        }, 250);
    }, 5000);
});
</script>
@endpush
