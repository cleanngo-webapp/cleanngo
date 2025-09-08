@extends('layouts.admin')

@section('title','Bookings and Scheduling')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-center">Bookings and Scheduling</h1>

    <div class="mt-6 border rounded-xl bg-white p-8 h-64 flex items-center justify-center text-gray-500">
        <div class="text-center">
            <p class="font-semibold">calendar where scheduled services</p>
            <p class="font-semibold">are here</p>
        </div>
    </div>

    <div class="mt-6 overflow-auto">
        <div class="flex justify-end mb-2">
            <button class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer">+ Add Booking and Schedule</button>
        </div>
        <div class="min-w-[900px]">
            <div class="grid grid-cols-8 text-sm font-semibold">
                <div class="p-2">Booking ID</div>
                <div class="p-2">Date & Time</div>
                <div class="p-2">Customer</div>
                <div class="p-2">Services</div>
                <div class="p-2">Assigned Employee</div>
                <div class="p-2">Status</div>
                <div class="p-2">Actions</div>
                <div class="p-2"></div>
            </div>
            <div class="grid grid-cols-8 bg-white rounded border text-sm">
                <div class="p-2">B001</div>
                <div class="p-2">09/20/25 09:00</div>
                <div class="p-2">Vina Lopez</div>
                <div class="p-2">window cleaning</div>
                <div class="p-2">Jose Lee</div>
                <div class="p-2">Pending</div>
                <div class="p-2">...</div>
                <div class="p-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection


