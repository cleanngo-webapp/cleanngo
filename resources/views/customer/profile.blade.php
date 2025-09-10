@extends('layouts.app')

@section('title','Profile')

@section('content')
{{-- Customer Profile: Bookings tracker and Address book --}}

<div class="max-w-4xl mx-auto pt-20 pb-10 p-6">
	<h1 class="text-2xl font-bold">Bookings and Addresses</h1>
	<p class="mt-2 text-gray-600">Track your bookings and manage your addresses.</p>

	<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Upcoming Bookings</h2>
			<p class="text-sm text-gray-500">You have no upcoming bookings.</p>
		</div>
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Addresses</h2>
			<p class="text-sm text-gray-500">Add or select your service location.</p>

			<form method="POST" action="{{ route('customer.address.store') }}" class="mt-3 space-y-2">
				@csrf
				<div class="grid grid-cols-2 gap-2">
					<input name="label" class="border rounded px-2 py-1" placeholder="Label (Home, Office)">
					<input name="line1" required class="border rounded px-2 py-1 col-span-2" placeholder="Address line">
					<input name="city" class="border rounded px-2 py-1" placeholder="City/Municipality">
					<input name="province" class="border rounded px-2 py-1" placeholder="Province">
					<input name="postal_code" class="border rounded px-2 py-1" placeholder="Postal Code/Zip Code">
				</div>

				<div id="map" class="h-64 rounded border"></div>
				<input type="hidden" name="latitude" id="lat">
				<input type="hidden" name="longitude" id="lng">
				<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_primary" value="1"> Set as primary</label>
				<button class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Save Address</button>
			</form>

			<div class="mt-4">
				<h3 class="font-semibold mb-2">Your Addresses</h3>
				@forelse(($addresses ?? []) as $addr)
					<div class="border rounded p-2 mb-2 flex items-center justify-between">
						<div>
							<div class="font-medium">{{ $addr->label ?? 'Address' }} @if($addr->is_primary) <span class="text-xs text-emerald-700">(Primary)</span> @endif</div>
							<div class="text-sm text-gray-600">{{ $addr->line1 }} {{ $addr->city ? ', '.$addr->city : '' }} {{ $addr->province ? ', '.$addr->province : '' }}</div>
							@if($addr->latitude && $addr->longitude)
								<div class="text-xs text-gray-500">Lat: {{ $addr->latitude }}, Lng: {{ $addr->longitude }}</div>
							@endif
						</div>
						<div class="flex items-center gap-2">
							<form method="POST" action="{{ route('customer.address.primary', $addr->id) }}">
								@csrf
								<button class="px-2 py-1 text-sm rounded border cursor-pointer hover:bg-emerald-700 hover:text-white w-28 whitespace-nowrap">Make Primary</button>
							</form>
							<form method="POST" action="{{ route('customer.address.destroy', $addr->id) }}">
								@csrf
								@method('DELETE')
								<button class="px-2 py-1 text-sm rounded border text-red-600 cursor-pointer hover:bg-red-600 hover:text-white w-20 whitespace-nowrap">Delete</button>
							</form>
						</div>
					</div>
				@empty
					<p class="text-sm text-gray-500">No addresses yet.</p>
				@endforelse
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([14.5995, 120.9842], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    function setLatLng(latlng) {
        if (!marker) {
            marker = L.marker(latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(e){
                var p = marker.getLatLng();
                document.getElementById('lat').value = p.lat;
                document.getElementById('lng').value = p.lng;
            });
        } else {
            marker.setLatLng(latlng);
        }
        document.getElementById('lat').value = latlng.lat;
        document.getElementById('lng').value = latlng.lng;
    }

    map.on('click', function(e) { setLatLng(e.latlng); });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos){
            var latlng = {lat: pos.coords.latitude, lng: pos.coords.longitude};
            map.setView(latlng, 15);
            setLatLng(latlng);
        });
    }
});
</script>
@endpush


