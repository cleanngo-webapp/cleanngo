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
							<form id="make-primary-{{ $addr->id }}" method="POST" action="{{ route('customer.address.primary', $addr->id) }}">@csrf</form>
							<form id="delete-address-{{ $addr->id }}" method="POST" action="{{ route('customer.address.destroy', $addr->id) }}">@csrf @method('DELETE')</form>

							@if($addr->is_primary)
								<button class="px-2 py-1 text-sm rounded border w-28 whitespace-nowrap bg-gray-200 border-gray-300 text-gray-500 cursor-not-allowed" disabled>Primary</button>
							@else
								<button type="button" class=" bg-emerald-700 text-white px-2 py-1 text-sm rounded border cursor-pointer hover:bg-emerald-700/80 hover:text-white w-28 whitespace-nowrap" onclick="openPrimaryConfirm('make-primary-{{ $addr->id }}')">Make Primary</button>
							@endif
							<button type="button" class="bg-red-600 text-white px-2 py-1 text-sm rounded border cursor-pointer hover:bg-red-600/80 hover:text-white w-20 whitespace-nowrap" onclick="openDeleteConfirm('delete-address-{{ $addr->id }}')">Delete</button>
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

// Modal helpers for address actions
let pendingFormId = null;
function openPrimaryConfirm(formId){
    pendingFormId = formId;
    const m = document.getElementById('confirm-primary-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function openDeleteConfirm(formId){
    pendingFormId = formId;
    const m = document.getElementById('confirm-delete-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeModal(id){
    const m = document.getElementById(id);
    m.classList.add('hidden'); m.classList.remove('flex');
}
window.addEventListener('DOMContentLoaded', function(){
    var mp = document.getElementById('confirm-primary-yes');
    if (mp) mp.addEventListener('click', function(){ if(pendingFormId){ document.getElementById(pendingFormId).submit(); }});
    var del = document.getElementById('confirm-delete-yes');
    if (del) del.addEventListener('click', function(){ if(pendingFormId){ document.getElementById(pendingFormId).submit(); }});
});
</script>
@endpush

<!-- Confirm Make Primary Modal -->
<div id="confirm-primary-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl w-full max-w-sm p-4">
        <div class="font-semibold text-lg">Make Primary Address</div>
        <p class="text-sm text-gray-600 mt-1">Are you sure you want to make this the primary address?</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="px-3 py-2 rounded border cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeModal('confirm-primary-modal')">Cancel</button>
            <button id="confirm-primary-yes" type="button" class="px-3 py-2 rounded bg-emerald-700 text-white cursor-pointer hover:bg-emerald-700/90">Make Primary</button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirm-delete-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-xl w-full max-w-sm p-4">
        <div class="font-semibold text-lg">Delete Address</div>
        <p class="text-sm text-gray-600 mt-1">Are you sure you want to delete this address?</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="px-3 py-2 rounded border cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeModal('confirm-delete-modal')">Cancel</button>
            <button id="confirm-delete-yes" type="button" class="px-3 py-2 rounded bg-red-600 text-white cursor-pointer hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>


