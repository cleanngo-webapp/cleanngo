@extends('layouts.employee')

@section('title','My Jobs')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold">My Jobs</h1>

    <div class="mt-4 flex items-center gap-4">
        <input type="text" placeholder="Search" class="border rounded px-3 py-2 w-96">
        <div class="ml-auto text-sm">
            <button class="px-3 py-2 bg-emerald-700 text-white rounded">Filter by Date</button>
            <button class="px-3 py-2 bg-emerald-700 text-white rounded ml-2">Filter by Service</button>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-xl border">
        <div class="p-3 font-semibold text-center">Today's Job List</div>
        <div class="grid grid-cols-8 text-sm font-semibold">
            <div class="p-2">Booking ID</div>
            <div class="p-2">Service Type</div>
            <div class="p-2">Customer Name</div>
            <div class="p-2">Time/Schedule</div>
            <div class="p-2">Status</div>
            <div class="p-2">Location</div>
            <div class="p-2">Action</div>
            <div class="p-2"></div>
        </div>
        <div class="grid grid-cols-8 text-sm">
            <div class="p-2">B001</div>
            <div class="p-2">Sofa Cleaning</div>
            <div class="p-2">Ana Cruz</div>
            <div class="p-2">Sept 12, 10:00 AM</div>
            <div class="p-2">Pending</div>
            <div class="p-2"><button class="px-2 py-1 bg-gray-200 rounded">VIEW MAP</button></div>
            <div class="p-2">[ Start Job ]</div>
            <div class="p-2"></div>
        </div>
    </div>
</div>
@endsection


