@extends('layouts.admin')

@section('title','Gallery Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-emerald-900">Gallery Management</h1>
        <p class="text-gray-600">Manage gallery images for each service</p>
    </div>

    {{-- Display success/error messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $service)
            <div onclick="window.location='{{ route('admin.gallery.service', $service['type']) }}'" 
                 class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col cursor-pointer hover:shadow-xl transition-shadow duration-300">
                
                {{-- Service Image --}}
                <div class="aspect-[4/3] bg-white relative">
                    <img src="{{ asset('assets/' . $service['image']) }}" 
                         alt="{{ $service['name'] }}" 
                         class="w-full h-full object-cover">
                    
                    {{-- Image Count Badge --}}
                    <div class="absolute top-3 right-3 bg-emerald-600 text-white px-2 py-1 rounded-full text-sm font-semibold">
                        {{ $service['image_count'] }} {{ $service['image_count'] == 1 ? 'image' : 'images' }}
                    </div>
                </div>
                
                {{-- Service Info --}}
                <div class="bg-emerald-700 text-white p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="text-lg font-semibold">{{ $service['name'] }}</div>
                        <p class="text-white/90 text-sm mt-2">{{ $service['description'] }}</p>
                    </div>
                    
                    {{-- Action Button --}}
                    <div class="mt-4 flex justify-center">
                        <span class="inline-block bg-white text-emerald-700 font-semibold px-4 py-2 rounded-full shadow hover:bg-gray-100 transition-colors duration-200">
                            Manage Images
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Instructions --}}
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">How to use Gallery Management:</h3>
        <ul class="text-blue-800 space-y-1">
            <li>• Click on any service card above to manage its gallery images</li>
            <li>• Upload new images, edit existing ones, or delete unwanted images</li>
            <li>• Images will automatically appear in the customer gallery view</li>
            <li>• Use the sort order feature to control image display sequence</li>
        </ul>
    </div>
</div>
@endsection


