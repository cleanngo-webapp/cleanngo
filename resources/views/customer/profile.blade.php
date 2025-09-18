@extends('layouts.app')

@section('title','Profile')

@section('content')
{{-- Customer Profile: Modern Bookings tracker and Address book --}}

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
	<div class="max-w-7xl mx-auto pt-20 pb-10 px-4 sm:px-6 lg:px-8">
		<!-- Header Section -->
		<div class="mb-8">
			<h1 class="text-3xl font-bold text-gray-900 mb-2">Profile</h1>
			<p class="text-lg text-gray-600">Manage your bookings and service addresses</p>
		</div>

		<!-- Main Content Grid -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
			<!-- Bookings Section - Takes 2 columns on large screens -->
			<div class="lg:col-span-2">
				<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
					<div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
						<div class="flex items-center justify-between">
							<div>
								<h2 class="text-xl font-semibold text-white">My Bookings</h2>
								<p class="text-emerald-100 text-sm mt-1">Track your service appointments</p>
							</div>
							<div class="bg-white/20 rounded-full p-3">
								<i class="ri-calendar-check-line text-2xl text-white"></i>
							</div>
						</div>
					</div>
					
					<div class="p-6">
						<!-- Search and Sort Section -->
						<div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
							<div class="flex flex-col lg:flex-row gap-4">
								<!-- Search Input -->
								<div class="flex-1">
									<label class="block text-sm font-medium text-gray-700 mb-2">Search Bookings</label>
									<div class="relative">
										<input type="text" 
											   id="booking-search" 
											   placeholder="Search by service type, employee name, booking code, or address..."
											   class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
										<i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
									</div>
								</div>
								
								<!-- Sort Dropdown -->
								<div class="lg:w-48">
									<label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
									<select id="booking-sort" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
										<option value="date_desc">Date (Newest First)</option>
										<option value="date_asc">Date (Oldest First)</option>
										<option value="amount_desc">Amount (Highest First)</option>
										<option value="amount_asc">Amount (Lowest First)</option>
										<option value="status">Status</option>
										<option value="service">Service</option>
										<option value="employee">Employee</option>
									</select>
								</div>
								
							</div>
							
							<!-- Loading Indicator -->
							<div id="search-loading" class="hidden mt-4 text-center">
								<div class="inline-flex items-center gap-2 text-emerald-600">
									<div class="loading-dots w-2 h-2 bg-emerald-600 rounded-full"></div>
									<div class="loading-dots w-2 h-2 bg-emerald-600 rounded-full"></div>
									<div class="loading-dots w-2 h-2 bg-emerald-600 rounded-full"></div>
									<span class="text-sm">Searching bookings...</span>
								</div>
							</div>
						</div>

						<!-- Bookings Container -->
						<div id="bookings-container">
							@if($bookings->count() > 0)
								<div class="space-y-4" id="bookings-list">
								@foreach($bookings as $booking)
									<div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200 bg-gray-50/50">
										<div class="flex items-start justify-between">
											<div class="flex-1">
												<div class="flex items-center gap-3 mb-2">
													@php
														$statusClasses = [
															'pending' => 'bg-orange-100 text-orange-800',
															'completed' => 'bg-green-100 text-green-800',
															'confirmed' => 'bg-blue-100 text-blue-800',
															'in_progress' => 'bg-yellow-100 text-yellow-800',
															'cancelled' => 'bg-red-100 text-red-800'
														];
														$statusClass = $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800';
													@endphp
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
														{{ $booking->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $booking->status)) }}
													</span>
													<span class="text-sm font-mono text-gray-500">#{{ $booking->code }}</span>
												</div>
												
												<h3 class="font-semibold text-gray-900 mb-1">{{ $serviceSummaries[$booking->id] ?? ($booking->service_name ?? 'General Service') }}</h3>
												<p class="text-sm text-gray-600 mb-2">{{ $booking->full_address ?? 'Address not specified' }}</p>
												
												<div class="flex items-center gap-4 text-sm text-gray-500">
													<div class="flex items-center gap-1">
														<i class="ri-calendar-line"></i>
														<span>{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('M j, Y') }}</span>
													</div>
													<div class="flex items-center gap-1">
														<i class="ri-time-line"></i>
														<span>{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('g:i A') }}</span>
													</div>
													@if($booking->employee_name)
														<div class="flex items-center gap-1">
															<i class="ri-user-line"></i>
															<span>{{ $booking->employee_name }}</span>
														</div>
													@endif
												</div>
											</div>
											
											<div class="text-right">
												<div class="text-lg font-semibold text-gray-900">
													₱{{ number_format($booking->total_due_cents / 100, 2) }}
												</div>
												<div class="text-xs text-gray-500 capitalize">
													@if($booking->status === 'completed' && $booking->payment_proof_status === 'approved')
														Paid
													@else
														{{ str_replace('_', ' ', $booking->payment_proof_status ?? $booking->payment_status) }}
													@endif
												</div>
												@if($booking->status === 'completed' && $booking->payment_proof_status === 'approved' && $booking->payment_method)
													<div class="text-xs text-gray-400 mt-1">
														<i class="ri-bank-card-line mr-1"></i>
														{{ ucfirst($booking->payment_method) }}
													</div>
												@endif
												@if($booking->status === 'pending')
													<div class="mt-2 flex gap-2">
														<button onclick="openCancelModal({{ $booking->id }}, '{{ $booking->code }}')" 
																class="px-3 py-1 bg-red-500 text-white text-xs rounded cursor-pointer hover:bg-red-600 transition-colors duration-200">
															Cancel Booking
														</button>
													</div>
												@elseif($booking->status === 'in_progress')
													<div class="mt-2">
														@if($paymentSettings && $paymentSettings->qr_code_path)
														<button onclick="openPaymentQRModal({{ $booking->id }}, '{{ $booking->code }}', {{ $booking->total_due_cents }})" 
																class="px-3 py-1 bg-blue-500 text-white text-xs rounded cursor-pointer hover:bg-blue-600 transition-colors duration-200">
															<i class="ri-qr-code-line mr-1"></i>
															View QR Code
														</button>
														@endif
													</div>
												@elseif($booking->status === 'completed' && $booking->payment_proof_status === 'approved')
													<div class="mt-2">
														<button onclick="openCustomerReceipt({{ $booking->id }})" 
																class="px-3 py-1 bg-emerald-600 text-white text-xs rounded cursor-pointer hover:bg-emerald-700 transition-colors duration-200">
															<i class="ri-receipt-line mr-1"></i>
															View Receipt
														</button>
													</div>
												@endif
											</div>
										</div>
									</div>
								@endforeach
								</div>
							@else
							<div class="text-center py-12">
								<div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
									<i class="ri-calendar-line text-2xl text-gray-400"></i>
								</div>
								<h3 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h3>
								<p class="text-gray-500 mb-4">You haven't made any service bookings yet.</p>
								<a href="{{ route('customer.services') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
									<i class="ri-add-line mr-2"></i>
									Book a Service
								</a>
							</div>
						@endif
						</div>
					</div>
				</div>
			</div>

			<!-- Address Management Section -->
			<div class="lg:col-span-1">
				<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
					<div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
						<div class="flex items-center justify-between">
							<div>
								<h2 class="text-xl font-semibold text-white">Addresses</h2>
								<p class="text-blue-100 text-sm mt-1">Manage service locations</p>
							</div>
							<div class="bg-white/20 rounded-full p-3">
								<i class="ri-map-pin-line text-2xl text-white"></i>
							</div>
						</div>
					</div>
					
					<div class="p-6">
						<!-- Add New Address Form -->
						<div class="mb-6">
							<h3 class="font-semibold text-gray-900 mb-3">Add New Address</h3>

							<form method="POST" action="{{ route('customer.address.store') }}" class="space-y-4">
								@csrf
								
								<!-- Address Line 1 -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-1">Address Line</label>
									<input name="line1" required 
										class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" 
										placeholder="Enter your address">
								</div>
								
								<!-- Location Details Grid -->
								<div class="grid grid-cols-2 gap-3">
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
										<input name="barangay" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" 
											placeholder="Barangay">
									</div>
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1">City</label>
										<input name="city" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" 
											placeholder="City/Municipality">
									</div>
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
										<input name="province" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" 
											placeholder="Province">
									</div>
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
										<input name="postal_code" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" 
											placeholder="Postal Code">
									</div>
								</div>
								
								<!-- Address Type and Auto Locate -->
								<div class="flex gap-3">
									<div class="flex-1">
										<label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
										<select name="label" 
											class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
											<option value="home">Home</option>
											<option value="office">Office</option>
										</select>
									</div>
									<div class="flex items-end">
										<button type="button" id="auto-locate" 
											class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2 cursor-pointer">
											<i class="ri-map-pin-line"></i>
											<span class="hidden sm:inline">Auto Locate</span>
										</button>
									</div>
								</div>

								<!-- Map Container -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-1">Location Map</label>
									<div id="map" class="h-48 rounded-lg border border-gray-300 overflow-hidden"></div>
									<input type="hidden" name="latitude" id="lat">
									<input type="hidden" name="longitude" id="lng">
								</div>
								
								<!-- Primary Address Checkbox -->
								<div class="flex items-center">
									<input type="checkbox" name="is_primary" value="1" id="is_primary" 
										class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded cursor-pointer">
									<label for="is_primary" class="ml-2 block text-sm text-gray-700">
										Set as primary address
									</label>
								</div>
								
								<!-- Save Button -->
								<button type="button" onclick="openSaveAddressConfirm()" 
									class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition-colors font-medium cursor-pointer">
									<i class="ri-save-line mr-2"></i>
									Save Address
								</button>
							</form>
						</div>

						<!-- Manage Existing Addresses -->
						<div>
							<h3 class="font-semibold text-gray-900 mb-4">Your Addresses</h3>
							@forelse(($addresses ?? []) as $addr)
								<div class="border border-gray-200 rounded-xl p-4 mb-3 hover:shadow-md transition-all duration-200 bg-gray-50/50">
									<div class="flex flex-col gap-3">
										<!-- Address header with label and primary badge -->
										<div class="flex items-center justify-between">
											<div class="flex items-center gap-2">
												<div class="flex items-center gap-1">
													@if($addr->label === 'home')
														<i class="ri-home-line text-emerald-600"></i>
													@else
														<i class="ri-building-line text-emerald-600"></i>
													@endif
													<span class="font-medium text-gray-900">{{ ucfirst($addr->label ?? 'Address') }}</span>
												</div>
												@if($addr->is_primary)
													<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
														<i class="ri-star-fill mr-1"></i>
														Primary
													</span>
												@endif
											</div>
										</div>
										
										<!-- Address content and action buttons -->
										<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
											<div class="flex-1 min-w-0">
												<div class="text-sm text-gray-600 mb-1 break-words">
													{{ $addr->line1 }}{{ $addr->barangay ? ', '.$addr->barangay : '' }}{{ $addr->city ? ', '.$addr->city : '' }}{{ $addr->province ? ', '.$addr->province : '' }}
												</div>
												@if($addr->postal_code)
													<div class="text-xs text-gray-500">{{ $addr->postal_code }}</div>
												@endif
											</div>
											
											<!-- Action buttons - always in column layout -->
											<div class="flex flex-col gap-2 flex-shrink-0">
												<form id="make-primary-{{ $addr->id }}" method="POST" action="{{ route('customer.address.primary', $addr->id) }}">@csrf</form>
												<form id="delete-address-{{ $addr->id }}" method="POST" action="{{ route('customer.address.destroy', $addr->id) }}">
													@csrf 
													@method('DELETE')
												</form>

												@if($addr->is_primary)
													<button class="px-3 py-1.5 text-xs rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed whitespace-nowrap w-full" disabled>
														<i class="ri-star-fill mr-1"></i>
														Primary
													</button>
												@else
													<button type="button" class="px-3 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors cursor-pointer whitespace-nowrap w-full" onclick="openPrimaryConfirm('make-primary-{{ $addr->id }}')">
														<i class="ri-star-line mr-1"></i>
														Make Primary
													</button>
												@endif
												<button type="button" class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors cursor-pointer whitespace-nowrap w-full" onclick="checkAddressCount({{ $addresses->count() }}, 'delete-address-{{ $addr->id }}')" data-address-id="{{ $addr->id }}">
													<i class="ri-delete-bin-line mr-1"></i>
													Delete
												</button>
											</div>
										</div>
									</div>
								</div>
							@empty
								<div class="text-center py-8">
									<div class="bg-gray-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
										<i class="ri-map-pin-line text-xl text-gray-400"></i>
									</div>
									<p class="text-sm text-gray-500">No addresses saved yet</p>
									<p class="text-xs text-gray-400 mt-1">Add your first address above</p>
								</div>
							@endforelse
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Receipt Modal Component -->
@include('components.receipt-modal', [
    'modalId' => 'customer-receipt-modal',
    'receiptData' => $receiptData ?? [],
    'bookingId' => null,
    'title' => 'Receipt',
    'showPaymentMethod' => true
])

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([13.6218, 123.1948], 12);
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

    // Auto Locate button
    var autoBtn = document.getElementById('auto-locate');
    function handleLocateSuccess(pos){
        var latlng = {lat: pos.coords.latitude, lng: pos.coords.longitude};
        map.setView(latlng, 16);
        setLatLng(latlng);
    }
    function handleLocateError(err){
        console.warn('Geolocation error', err);
        alert('Unable to get your location. Please allow location permission and try again. If you are not on http://localhost or https, the browser may block geolocation.');
    }
    if (autoBtn) {
        autoBtn.addEventListener('click', function(){
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            navigator.geolocation.getCurrentPosition(handleLocateSuccess, handleLocateError, {enableHighAccuracy:true, timeout:10000, maximumAge:0});
        });
    }
});

// Modal helpers for address actions
var pendingFormId = null;
function openPrimaryConfirm(formId){
    console.log('Opening primary confirm for:', formId);
    pendingFormId = formId;
    const m = document.getElementById('confirm-primary-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function openDeleteConfirm(formId){
    console.log('Opening delete confirm for:', formId);
    pendingFormId = formId;
    const m = document.getElementById('confirm-delete-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function openSaveAddressConfirm(){
    console.log('Opening save address confirm');
    const m = document.getElementById('confirm-save-address-modal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeModal(id){
    const m = document.getElementById(id);
    m.classList.add('hidden'); m.classList.remove('flex');
}
function submitAddressForm(){
    console.log('Submitting address form');
    // Look specifically for the address form by finding the form that contains the line1 input
    const line1Input = document.querySelector('input[name="line1"]');
    if (line1Input) {
        const form = line1Input.closest('form');
        if (form) {
            console.log('Found address form:', form);
            form.submit();
        } else {
            console.error('Could not find form containing line1 input');
            alert('Error: Address form not found. Please refresh the page and try again.');
        }
    } else {
        console.error('Line1 input not found');
        alert('Error: Address form not found. Please refresh the page and try again.');
    }
}
function submitPendingForm(){
    console.log('submitPendingForm called with pendingFormId:', pendingFormId);
    if (pendingFormId) {
        var f = document.getElementById(pendingFormId);
        if (f) { 
            console.log('Submitting form:', pendingFormId, f.action, f.method);
            // Ensure the form has the correct method and CSRF token
            if (f.method.toLowerCase() !== 'post') {
                f.method = 'POST';
            }
            // Check if CSRF token exists
            var csrfToken = f.querySelector('input[name="_token"]');
            if (!csrfToken) {
                console.error('CSRF token missing from form');
                return;
            }
            // For primary address, we don't need DELETE method override
            var methodField = f.querySelector('input[name="_method"]');
            if (methodField && methodField.value === 'DELETE') {
                // This is a delete form, check method override
                if (!methodField) {
                    console.error('Method override missing from form');
                    return;
                }
            }
            f.submit(); 
        } else {
            console.error('Form not found:', pendingFormId);
        }
    } else {
        console.error('No pending form ID');
    }
}
// Attach immediately in case this script executes after DOM is ready
(function(){
    var mp = document.getElementById('confirm-primary-yes');
    if (mp) mp.addEventListener('click', submitPendingForm);
    var del = document.getElementById('confirm-delete-yes');
    if (del) del.addEventListener('click', submitPendingForm);
    var save = document.getElementById('confirm-save-address-yes');
    if (save) save.addEventListener('click', submitAddressForm);
})();

// Also attach on DOM ready as backup
document.addEventListener('DOMContentLoaded', function(){
    var mp = document.getElementById('confirm-primary-yes');
    if (mp) mp.addEventListener('click', submitPendingForm);
    var del = document.getElementById('confirm-delete-yes');
    if (del) del.addEventListener('click', submitPendingForm);
    var save = document.getElementById('confirm-save-address-yes');
    if (save) save.addEventListener('click', submitAddressForm);
});

// Cancel Booking Modal Functions
let currentBookingId = null;

function openCancelModal(bookingId, bookingCode) {
    currentBookingId = bookingId;
    document.getElementById('cancel-booking-code').textContent = bookingCode;
    document.getElementById('cancel-booking-modal').classList.remove('hidden');
    document.getElementById('cancel-booking-modal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancel-booking-modal').classList.add('hidden');
    document.getElementById('cancel-booking-modal').classList.remove('flex');
    currentBookingId = null;
}

function confirmCancelBooking() {
    if (!currentBookingId) return;
    
    // Set up the form action and submit
    const form = document.getElementById('cancel-booking-form');
    form.action = `/customer/bookings/${currentBookingId}/cancel`;
    form.submit();
}

// Close modal when clicking outside the modal content
document.getElementById('cancel-booking-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
    }
});

// Receipt modal function
function openCustomerReceipt(bookingId) {
    // Get payment method from the booking data
    const booking = @json($bookings);
    const currentBooking = booking.find(b => b.id == bookingId);
    const paymentMethod = currentBooking ? currentBooking.payment_method : null;
    
    openReceipt('customer-receipt-modal', bookingId, @json($receiptData ?? []), {
        showPaymentMethod: true,
        paymentMethod: paymentMethod
    });
}

// Address deletion validation functions
function checkAddressCount(addressCount, formId) {
    if (addressCount <= 1) {
        // Show the cannot delete modal
        openCannotDeleteModal();
    } else {
        // Proceed with normal delete confirmation
        openDeleteConfirm(formId);
    }
}

function openCannotDeleteModal() {
    document.getElementById('cannot-delete-address-modal').classList.remove('hidden');
    document.getElementById('cannot-delete-address-modal').classList.add('flex');
}

function closeCannotDeleteModal() {
    document.getElementById('cannot-delete-address-modal').classList.add('hidden');
    document.getElementById('cannot-delete-address-modal').classList.remove('flex');
}

// Close cannot delete modal when clicking outside
document.getElementById('cannot-delete-address-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCannotDeleteModal();
    }
});

// Payment QR Modal Functions
function openPaymentQRModal(bookingId, bookingCode, totalAmount) {
    document.getElementById('payment-booking-code').textContent = bookingCode;
    document.getElementById('payment-amount').textContent = (totalAmount / 100).toFixed(2);
    document.getElementById('payment-qr-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePaymentQRModal() {
    document.getElementById('payment-qr-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close payment QR modal when clicking outside
document.getElementById('payment-qr-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentQRModal();
    }
});

// Search and Sort Functionality
let searchTimeout;
let currentSearchTerm = '';
let currentSortBy = 'date_desc';

// Initialize search and sort functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('booking-search');
    const sortSelect = document.getElementById('booking-sort');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            currentSearchTerm = this.value.trim();
            
            // If search is cleared, reload the page to show all bookings
            if (currentSearchTerm === '') {
                window.location.reload();
                return;
            }
            
            // Show loading indicator
            showSearchLoading();
            
            // Debounce search to avoid too many requests
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSortBy = this.value;
            showSearchLoading();
            performSearch();
        });
    }
});

function showSearchLoading() {
    const loadingElement = document.getElementById('search-loading');
    if (loadingElement) {
        loadingElement.classList.remove('hidden');
    }
}

function hideSearchLoading() {
    const loadingElement = document.getElementById('search-loading');
    if (loadingElement) {
        loadingElement.classList.add('hidden');
    }
}

function performSearch() {
    // Create form data for AJAX request
    const formData = new FormData();
    formData.append('search', currentSearchTerm);
    formData.append('sort', currentSortBy);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Make AJAX request
    fetch('{{ route("customer.bookings.search") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideSearchLoading();
        console.log('Search response:', data); // Debug logging
        if (data.success) {
            updateBookingsList(data.bookings, data.serviceSummaries);
        } else {
            console.error('Search failed:', data.message);
            showSearchError();
        }
    })
    .catch(error => {
        hideSearchLoading();
        console.error('Search error:', error);
        showSearchError();
    });
}

function updateBookingsList(bookings, serviceSummaries) {
    const bookingsList = document.getElementById('bookings-list');
    const bookingsContainer = document.getElementById('bookings-container');
    
    if (!bookingsList || !bookingsContainer) return;
    
    if (bookings.length === 0) {
        // Show no results message
        bookingsContainer.innerHTML = `
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="ri-search-line text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                <p class="text-gray-500 mb-4">Try adjusting your search criteria or clear the filters.</p>
                <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    <i class="ri-refresh-line mr-2"></i>
                    Reload Bookings
                </button>
            </div>
        `;
        return;
    }
    
    // Generate HTML for bookings
    let bookingsHTML = '<div class="space-y-4" id="bookings-list">';
    
    bookings.forEach(booking => {
        const statusClasses = {
            'pending': 'bg-orange-100 text-orange-800',
            'completed': 'bg-green-100 text-green-800',
            'confirmed': 'bg-blue-100 text-blue-800',
            'in_progress': 'bg-yellow-100 text-yellow-800',
            'cancelled': 'bg-red-100 text-red-800'
        };
        const statusClass = statusClasses[booking.status] || 'bg-gray-100 text-gray-800';
        
        bookingsHTML += `
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200 bg-gray-50/50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${booking.status === 'in_progress' ? 'In Progress' : booking.status.charAt(0).toUpperCase() + booking.status.slice(1).replace('_', ' ')}
                            </span>
                            <span class="text-sm font-mono text-gray-500">#${booking.code}</span>
                        </div>
                        
                        <h3 class="font-semibold text-gray-900 mb-1">${serviceSummaries[booking.id] || (booking.service_name || 'General Service')}</h3>
                        <p class="text-sm text-gray-600 mb-2">${booking.full_address || 'Address not specified'}</p>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <div class="flex items-center gap-1">
                                <i class="ri-calendar-line"></i>
                                <span>${new Date(booking.scheduled_start).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="ri-time-line"></i>
                                <span>${new Date(booking.scheduled_start).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                            </div>
                            ${booking.employee_name ? `
                                <div class="flex items-center gap-1">
                                    <i class="ri-user-line"></i>
                                    <span>${booking.employee_name}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-lg font-semibold text-gray-900">
                            ₱${(booking.total_due_cents / 100).toFixed(2)}
                        </div>
                        <div class="text-xs text-gray-500 capitalize">
                            ${booking.status === 'completed' && booking.payment_proof_status === 'approved' ? 'Paid' : (booking.payment_proof_status || booking.payment_status || '').replace('_', ' ')}
                        </div>
                        ${booking.status === 'completed' && booking.payment_proof_status === 'approved' && booking.payment_method ? `
                            <div class="text-xs text-gray-400 mt-1">
                                <i class="ri-bank-card-line mr-1"></i>
                                ${booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1)}
                            </div>
                        ` : ''}
                        ${getBookingActions(booking)}
                    </div>
                </div>
            </div>
        `;
    });
    
    bookingsHTML += '</div>';
    bookingsContainer.innerHTML = bookingsHTML;
}

function getBookingActions(booking) {
    let actions = '';
    
    if (booking.status === 'pending') {
        actions = `
            <div class="mt-2 flex gap-2">
                <button onclick="openCancelModal(${booking.id}, '${booking.code}')" 
                        class="px-3 py-1 bg-red-500 text-white text-xs rounded cursor-pointer hover:bg-red-600 transition-colors duration-200">
                    Cancel Booking
                </button>
            </div>
        `;
    } else if (booking.status === 'in_progress') {
        actions = `
            <div class="mt-2">
                <button onclick="openPaymentQRModal(${booking.id}, '${booking.code}', ${booking.total_due_cents})" 
                        class="px-3 py-1 bg-blue-500 text-white text-xs rounded cursor-pointer hover:bg-blue-600 transition-colors duration-200">
                    <i class="ri-qr-code-line mr-1"></i>
                    View QR Code
                </button>
            </div>
        `;
    } else if (booking.status === 'completed' && booking.payment_proof_status === 'approved') {
        actions = `
            <div class="mt-2">
                <button onclick="openCustomerReceipt(${booking.id})" 
                        class="px-3 py-1 bg-emerald-600 text-white text-xs rounded cursor-pointer hover:bg-emerald-700 transition-colors duration-200">
                    <i class="ri-receipt-line mr-1"></i>
                    View Receipt
                </button>
            </div>
        `;
    }
    
    return actions;
}


function showSearchError() {
    const bookingsContainer = document.getElementById('bookings-container');
    if (bookingsContainer) {
        bookingsContainer.innerHTML = `
            <div class="text-center py-12">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="ri-error-warning-line text-2xl text-red-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Search Error</h3>
                <p class="text-gray-500 mb-4">There was an error searching your bookings. Please try again.</p>
                <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    <i class="ri-refresh-line mr-2"></i>
                    Reload Bookings
                </button>
            </div>
        `;
    }
}
</script>
@endpush

<!-- Payment QR Code Modal -->
<div id="payment-qr-modal" class="fixed inset-0 bg-black/50 z-9999 items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto mt-16">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
            <button onclick="closePaymentQRModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="text-center">
            <div class="mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Booking #<span id="payment-booking-code"></span></h4>
                <p class="text-2xl font-bold text-emerald-600">₱<span id="payment-amount"></span></p>
            </div>
            
            @if($paymentSettings && $paymentSettings->qr_code_path)
            <div class="mb-4">
                <img src="{{ Storage::url($paymentSettings->qr_code_path) }}" 
                     alt="GCash QR Code" 
                     class="w-48 h-48 object-contain border border-gray-200 rounded-lg mx-auto">
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h5 class="font-semibold text-gray-900 mb-2">Payment Details</h5>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><span class="font-medium">GCash Name:</span> {{ $paymentSettings->gcash_name }}</p>
                    <p><span class="font-medium">GCash Number:</span> {{ $paymentSettings->gcash_number }}</p>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <i class="ri-information-line text-blue-500 text-lg mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        <div class="font-medium mb-1">Payment Instructions</div>
                        <div>1. Open your GCash app</div>
                        <div>2. Scan the QR code above</div>
                        <div>3. Enter the exact amount shown</div>
                        <div>4. Complete the payment</div>
                        <div>5. Upload payment proof when done</div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="ri-error-warning-line text-yellow-500 text-lg"></i>
                    <div class="text-sm text-yellow-700">
                        <div class="font-medium">Payment information not available</div>
                        <div>Please contact support for payment details.</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirm Make Primary Modal -->
<div id="confirm-primary-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-blue-100 rounded-full p-2">
                <i class="ri-star-line text-blue-600 text-xl"></i>
            </div>
            <div>
                <div class="font-semibold text-lg text-gray-900">Make Primary Address</div>
                <p class="text-sm text-gray-600">Set this as your default service location</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to make this the primary address? This will be used as the default location for new bookings.</p>
        <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer" onclick="closeModal('confirm-primary-modal')">Cancel</button>
            <button id="confirm-primary-yes" type="button" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors cursor-pointer" onclick="submitPendingForm()">
                <i class="ri-star-fill mr-2"></i>
                Make Primary
            </button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirm-delete-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-red-100 rounded-full p-2">
                <i class="ri-delete-bin-line text-red-600 text-xl"></i>
            </div>
            <div>
                <div class="font-semibold text-lg text-gray-900">Delete Address</div>
                <p class="text-sm text-gray-600">This action cannot be undone</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this address? Any upcoming bookings at this location may be affected.</p>
        <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer" onclick="closeModal('confirm-delete-modal')">Cancel</button>
            <button id="confirm-delete-yes" type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer" onclick="if(window.pendingFormId){ var f=document.getElementById(window.pendingFormId); if(f){ f.submit(); } }">
                <i class="ri-delete-bin-fill mr-2"></i>
                Delete Address
            </button>
        </div>
    </div>
</div>

<!-- Confirm Save Address Modal -->
<div id="confirm-save-address-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-green-100 rounded-full p-2">
                <i class="ri-save-line text-green-600 text-xl"></i>
            </div>
            <div>
                <div class="font-semibold text-lg text-gray-900">Save Address</div>
                <p class="text-sm text-gray-600">Add this location to your address book</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to save this address? You can use it for future service bookings.</p>
        <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer" onclick="closeModal('confirm-save-address-modal')">Cancel</button>
            <button id="confirm-save-address-yes" type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer" onclick="submitAddressForm()">
                <i class="ri-save-fill mr-2"></i>
                Save Address
            </button>
        </div>
    </div>
</div>

<!-- Cancel Booking Confirmation Modal -->
<div id="cancel-booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
    <div class="bg-white rounded-xl w-full max-w-md p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-lg">Cancel Booking</div>
            <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeCancelModal()">✕</button>
        </div>
        
        <div class="space-y-4">
            <div class="text-sm text-gray-600">
                Are you sure you want to cancel booking <span id="cancel-booking-code" class="font-mono font-semibold text-gray-900"></span>?
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <i class="ri-error-warning-line text-red-500 text-lg mt-0.5"></i>
                    <div class="text-sm text-red-700">
                        <div class="font-medium mb-1">This action cannot be undone.</div>
                        <div>Once cancelled, you'll need to create a new booking if you want to reschedule.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hidden form for cancellation -->
        <form id="cancel-booking-form" method="POST" class="hidden">
            @csrf
        </form>
        
        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded cursor-pointer hover:bg-gray-600 transition-colors duration-200" onclick="closeCancelModal()">
                Keep Booking
            </button>
            <button type="button" class="px-4 py-2 bg-red-600 text-white rounded cursor-pointer hover:bg-red-700 transition-colors duration-200" onclick="confirmCancelBooking()">
                Cancel Booking
            </button>
        </div>
    </div>
</div>

<!-- Cannot Delete Last Address Modal -->
<div id="cannot-delete-address-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
    <div class="bg-white rounded-xl w-full max-w-md p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-lg">Cannot Delete Address</div>
            <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeCannotDeleteModal()">✕</button>
        </div>
        
        <div class="space-y-4">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <i class="ri-error-warning-line text-orange-500 text-lg mt-0.5"></i>
                    <div class="text-sm text-orange-700">
                        <div class="font-medium mb-1">You cannot delete your last address.</div>
                        <div>You must have at least one address saved to use our services.</div>
                    </div>
                </div>
            </div>
            
            <div class="text-sm text-gray-600">
                To delete this address, please:
            </div>
            
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <i class="ri-add-line text-emerald-600"></i>
                    <span>Add a new address first</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="ri-star-line text-emerald-600"></i>
                    <span>Make it your primary address</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="ri-delete-bin-line text-emerald-600"></i>
                    <span>Then you can delete this one</span>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <button type="button" class="px-4 py-2 bg-emerald-600 text-white rounded cursor-pointer hover:bg-emerald-700 transition-colors duration-200" onclick="closeCannotDeleteModal()">
                <i class="ri-add-line mr-2"></i>
                Add New Address
            </button>
        </div>
    </div>
</div>

