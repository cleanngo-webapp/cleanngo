@extends('layouts.app')

@section('title','All Services')

@section('content')
<div class="max-w-7xl mx-auto pt-20">
    <h1 class="text-2xl md:text-2xl font-extrabold text-emerald-900">OFFERED SERVICES</h1>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Carpet Deep Cleaning --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-carpet-cleaning.webp') }}" alt="Carpet deep cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Carpet Deep Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Removes dirt and allergens to restore freshness and promote a healthier home.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>
        
        {{-- Enhanced Disinfection --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-home-dis.webp') }}" alt="Enhanced Disinfection" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Enhanced Disinfection</div>
                    <p class="text-white/90 text-sm mt-2">Advanced disinfection for safer homes and workplaces.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>

        {{-- Glass Cleaning --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-glass-cleaning.webp') }}" alt="Glass Cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Glass Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Streak-free shine for windows and glass surfaces.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>

        {{-- Home Service Car Interior Detailing --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-dashboard-car-detailing.webp') }}" alt="Home service car interior detailing" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Home Service Car Interior Detailing</div>
                    <p class="text-white/90 text-sm mt-2">Specialized deep cleaning right at your doorstep for a refreshed car interior.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>
        
        
        {{-- Post Construction Cleaning --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-post-cons-cleaning.webp') }}" alt="Post Construction Cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Post Construction Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Thorough cleanup to remove dust and debris for move-in ready spaces.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>

        {{-- Sofa/ Mattress Deep Cleaning --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
            <div class="aspect-[4/3] bg-white">
                <img src="{{ asset('assets/cs-services-sofa-mattress-cleaning.webp') }}" alt="Sofa/ mattress deep cleaning" class="w-full h-full object-cover">
            </div>
            <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="text-lg font-semibold">Sofa / Mattress Deep Cleaning</div>
                    <p class="text-white/90 text-sm mt-2">Eliminates dust, stains, and allergens to restore comfort and hygiene.</p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('customer.services') }}" class="inline-block bg-white text-gray-900 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100">Request an Estimate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
