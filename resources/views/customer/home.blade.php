@extends('layouts.app')

@section('title','Overview')

@section('content')
<div class="max-w-7xl mx-auto pt-7">
    {{-- Hero Section --}}
    <section class="relative w-screen -mx-[calc(50vw-50%)] rounded-2xl overflow-hidden shadow-md">
        <img src="{{ asset('assets/cs-dashborard-req-2.webp') }}" alt="Fresh Spaces, Happy Faces" class="w-full h-[600px] md:h-[500px] object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent"></div>
        <div class="absolute inset-0 flex flex-col justify-end px-6 md:px-10 pb-8 gap-4">
            <h1 class="text-white text-2xl md:text-4xl font-extrabold max-w-2xl">Fresh Spaces, Happy Faces</h1>
            <div>
                <a href="{{ route('customer.services') }}" class="inline-block bg-brand-green text-white font-semibold px-5 py-3 rounded-lg shadow hover:bg-emerald-700">Request an Estimate</a>
            </div>
        </div>
    </section>

    {{-- Our Services --}}
    <section class="mt-10">
        <h2 class="text-xl md:text-2xl font-extrabold text-emerald-900">Our Services</h2>
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-glass-cleaning.webp') }}" alt="Carpet Deep Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Carpet Deep Cleaning</div>
            </a>
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-home-dis.webp') }}" alt="Enhanced Disinfection" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Enhanced Disinfection</div>
            </a>
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-sofa-mattress-cleaning.webp') }}" alt="Sofa / Mattress Deep Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Sofa / Mattress Deep Cleaning</div>
            </a>
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-dashboard-car-detailing.webp') }}" alt="Car Interior Detailing" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Car Interior Detailing</div>
            </a>
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-glass-cleaning.webp') }}" alt="Glass Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Glass Cleaning</div>
            </a>
            <a href="{{ route('customer.services') }}" class="group bg-emerald-700 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-4 hover:ring-2 hover:ring-emerald-500 transition-all duration-300 ease-in-out">
                <div class="aspect-[4/3] bg-white flex items-center justify-center text-gray-500"><img src="{{ asset('assets/cs-services-post-cons-cleaning.webp') }}" alt="Post Construction Cleaning" class="w-full h-full object-cover"></div>
                <div class="px-2 py-1 text-center text-white text-sm font-semibold mt-2">Post Construction Cleaning</div>
            </a>
        </div>
    </section>

</div>
@endsection


