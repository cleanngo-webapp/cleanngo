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
            <div class="p-2"><button class="px-2 py-1 bg-gray-200 rounded" onclick="window.dispatchEvent(new CustomEvent('showJobMap',{detail:{lat:14.5995,lng:120.9842}}))">VIEW MAP</button></div>
            <div class="p-2">[ Start Job ]</div>
            <div class="p-2"></div>
        </div>
    </div>
    <div id="job-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Job Location</div>
                <button onclick="document.getElementById('job-map-modal').classList.add('hidden')">✕</button>
            </div>
            <div id="jobMap" class="h-80 rounded border"></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let jobMap, jobMarker;
window.addEventListener('showJobMap', function(e){
    const lat = e.detail.lat, lng = e.detail.lng;
    const modal = document.getElementById('job-map-modal');
    modal.classList.remove('hidden');
    setTimeout(function(){
        if(!jobMap){
            jobMap = L.map('jobMap').setView([lat,lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(jobMap);
        } else {
            jobMap.setView([lat,lng], 15);
        }
        if(!jobMarker){ jobMarker = L.marker([lat,lng]).addTo(jobMap); } else { jobMarker.setLatLng([lat,lng]); }
    }, 50);
});
</script>
@endpush