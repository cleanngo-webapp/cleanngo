@extends('layouts.employee')

@section('title','Employee Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
{{-- Employee Dashboard with job assignments and progress tracking --}}
{{-- Purpose: Daily jobs for cleaners, with simple instructions and progress tracking --}}

<div class="max-w-6xl mx-auto">
	<div class="flex items-center justify-between mb-8">
		<h1 class="text-3xl font-extrabold text-gray-900">Dashboard</h1>
		<div class="text-sm text-gray-500">
			{{ now()->format('l, F j, Y') }}
		</div>
	</div>

	{{-- Job Statistics Cards --}}
	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
		{{-- Jobs Assigned Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Active Jobs</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($jobsAssignedToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Scheduled today or in progress</p>
				</div>
				<div class="bg-blue-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Jobs Completed Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Jobs Completed Today</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($jobsCompletedToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Finished today</p>
				</div>
				<div class="bg-green-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Pending Jobs Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Pending Jobs</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($pendingJobs) }}</p>
					<p class="text-xs text-gray-500 mt-1">Awaiting start</p>
				</div>
				<div class="bg-yellow-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
			</div>
		</div>
	</div>

	{{-- Active Job Assignments - Compact Version --}}
	<div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
		<div class="p-4 border-b border-gray-100">
			<h2 class="text-lg font-semibold text-gray-900">Job Assignments</h2>
			<p class="text-xs text-gray-500 mt-1">Your upcoming jobs and current assignments</p>
		</div>
		<div class="p-4">
			@forelse($todayJobs as $job)
			<div class="border border-gray-200 rounded-lg p-3 mb-3 last:mb-0 hover:bg-gray-50 transition-colors">
				<div class="flex items-start justify-between">
					<div class="flex-1">
						<div class="flex items-center gap-2 mb-2">
							<h3 class="text-base font-semibold text-gray-900">{{ $serviceSummaries[$job->id] ?? ($job->service_name ?? 'General Service') }}</h3>
							@php
								$statusColors = [
									'pending' => 'bg-yellow-100 text-yellow-800',
									'confirmed' => 'bg-blue-100 text-blue-800',
									'in_progress' => 'bg-purple-100 text-purple-800',
									'completed' => 'bg-green-100 text-green-800',
									'cancelled' => 'bg-red-100 text-red-800',
									'no_show' => 'bg-gray-100 text-gray-800'
								];
							@endphp
							<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
								{{ $job->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $job->status)) }}
							</span>
						</div>
						
						<div class="text-xs text-gray-600 space-y-1">
							<p><span class="font-medium">Customer:</span> {{ $job->first_name }} {{ $job->last_name }}</p>
							<p><span class="font-medium">Phone:</span> {{ $job->phone }}</p>
							<p><span class="font-medium">Address:</span> {{ $job->street_address }}, {{ $job->city }}</p>
							<p><span class="font-medium">Scheduled:</span> {{ \Carbon\Carbon::parse($job->scheduled_start)->format('M j, Y g:i A') }}
								@if($job->scheduled_end)
									- {{ \Carbon\Carbon::parse($job->scheduled_end)->format('g:i A') }}
								@endif
							</p>
						</div>
						
						@if($job->notes)
						<div class="mt-2 p-2 bg-gray-50 rounded">
							<p class="text-xs text-gray-700"><span class="font-medium">Notes:</span> {{ $job->notes }}</p>
						</div>
						@endif
					</div>
					
					<div class="ml-3 flex flex-col gap-1">
						@if($job->status === 'confirmed' || $job->status === 'pending')
							@php
								$isScheduledToday = \Carbon\Carbon::parse($job->scheduled_start)->isToday();
								$canStartJob = $isScheduledToday || $job->status === 'in_progress';
							@endphp
							@if($canStartJob)
								<button type="button" onclick="confirmStartJob({{ $job->id }})" class="bg-emerald-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
									Start Job
								</button>
							@else
								<button type="button" disabled class="bg-gray-400 text-white px-3 py-1.5 rounded text-xs font-medium cursor-not-allowed" title="Job scheduled for {{ \Carbon\Carbon::parse($job->scheduled_start)->format('M j, Y') }}">
									Start Job
								</button>
							@endif
						@endif
						
						@if($paymentSettings && $paymentSettings->qr_code_path && ($job->status === 'pending' || $job->status === 'in_progress'))
						<button onclick="openEmpPaymentQRModal({{ $job->id }}, '{{ $job->code }}', {{ $job->total_due_cents ?? 0 }}, '{{ $job->status }}')" class="bg-purple-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-purple-700 transition-colors cursor-pointer">
							<i class="ri-qr-code-line"></i>
						</button>
						@endif
						
						<button onclick="openEmpLocation({{ json_encode(['id' => $job->id, 'lat' => $job->latitude, 'lng' => $job->longitude]) }})" class="bg-green-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-green-700 transition-colors cursor-pointer">
							<i class="ri-map-pin-line"></i>
						</button>
						
						<a href="{{ route('employee.jobs') }}" class="text-blue-600 text-xs font-medium hover:text-blue-800">
							View Details
						</a>
					</div>
				</div>
			</div>
			@empty
			<div class="text-center py-6">
				<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
					<i class="ri-briefcase-3-line text-2xl text-gray-400"></i>
				</div>
				<h3 class="mt-2 text-sm font-medium text-gray-900">No Upcoming Jobs</h3>
				<p class="mt-1 text-xs text-gray-500">You don't have any upcoming job assignments.</p>
			</div>
			@endforelse
		</div>
	</div>

	{{-- Calendar Section --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-100">
			<h2 class="text-xl font-semibold text-gray-900">Schedule Calendar</h2>
			<p class="text-sm text-gray-500 mt-1">View your upcoming job assignments</p>
		</div>
		<div class="p-6">
		<div id="employee-calendar" data-events-url="{{ route('employee.calendar.events') }}"></div>
		</div>
	</div>
</div>

<!-- Location Map Modal -->
<div id="job-map-modal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center" style="display: none;">
	<div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
		<div class="flex justify-between items-center mb-4">
			<h3 class="text-lg font-semibold text-gray-900">Job Location</h3>
			<button onclick="closeEmpLocation()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
		
		<div class="mb-4">
			<p id="empLocationAddress" class="text-sm text-gray-600 mb-1"></p>
			<p id="empLocationPhone" class="text-sm text-gray-500"></p>
		</div>
		
		<div id="jobMap" style="height: 400px; width: 100%;" class="rounded-lg border border-gray-200"></div>
	</div>
</div>

<!-- Employee Payment QR Code Modal -->
<div id="emp-payment-qr-modal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center" style="display: none;">
	<div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
		<div class="flex justify-between items-center mb-4">
			<h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
			<button onclick="closeEmpPaymentQRModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
		
		<div class="text-center">
			<div class="mb-4">
				<h4 class="text-lg font-semibold text-gray-900 mb-2">Job #<span id="emp-payment-booking-code"></span></h4>
				<p class="text-2xl font-bold text-emerald-600">₱<span id="emp-payment-amount"></span></p>
				<p class="text-sm text-gray-500 mt-1">Status: <span id="emp-payment-status" class="font-medium"></span></p>
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
						<div class="font-medium mb-1">Payment Instructions for Customer</div>
						<div>1. Open GCash app</div>
						<div>2. Scan the QR code above</div>
						<div>3. Enter the exact amount shown</div>
						<div>4. Complete the payment</div>
						<div>5. Show payment confirmation</div>
					</div>
				</div>
			</div>
			@else
			<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
				<div class="flex items-center gap-2">
					<i class="ri-error-warning-line text-yellow-500 text-lg"></i>
					<div class="text-sm text-yellow-700">
						<div class="font-medium">Payment information not available</div>
						<div>Please contact admin for payment details.</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let jobMap, jobMarker;

function openEmpLocation(payload){
    const lat = payload?.lat ?? 0, lng = payload?.lng ?? 0;
    const modal = document.getElementById('job-map-modal');
    modal.style.display = 'flex';
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
    const addr = document.getElementById('empLocationAddress');
    const phone = document.getElementById('empLocationPhone');
    
    // Find address and phone from server-provided locationsData if available
    try {
        const data = (window.empLocations && (window.empLocations[payload.id] || window.empLocations[String(payload.id)])) || null;
        if (data) {
            addr.textContent = data.address || '';
            phone.textContent = data.phone ? ('Contact: ' + data.phone) : '';
        } else {
            addr.textContent = '';
            phone.textContent = '';
        }
    } catch(e){ 
        addr.textContent=''; 
        phone.textContent=''; 
    }
    
    setTimeout(function(){
        if(!jobMap){
            jobMap = L.map('jobMap').setView([lat,lng], (lat&&lng)?15:5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                maxZoom: 19, 
                attribution: '&copy; OpenStreetMap' 
            }).addTo(jobMap);
        } else { 
            jobMap.setView([lat,lng], (lat&&lng)?15:5); 
        }
        if(!jobMarker){ 
            jobMarker = L.marker([lat,lng]).addTo(jobMap); 
        } else { 
            jobMarker.setLatLng([lat,lng]); 
        }
        setTimeout(()=>{ if(jobMap) jobMap.invalidateSize(true); }, 100);
    }, 50);
}

function closeEmpLocation(){
    const modal = document.getElementById('job-map-modal');
    modal.style.display = 'none';
    // Restore background scrolling
    document.body.style.overflow = 'auto';
}

// Make locations available globally for address/phone rendering
window.empLocations = @json($locationsData ?? []);

// Employee Payment QR Modal Functions
function openEmpPaymentQRModal(jobId, jobCode, totalAmount, status) {
    document.getElementById('emp-payment-booking-code').textContent = jobCode;
    document.getElementById('emp-payment-amount').textContent = (totalAmount / 100).toFixed(2);
    document.getElementById('emp-payment-status').textContent = status === 'in_progress' ? 'In Progress' : status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('emp-payment-qr-modal').style.display = 'flex';
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

function closeEmpPaymentQRModal() {
    document.getElementById('emp-payment-qr-modal').style.display = 'none';
    // Restore background scrolling
    document.body.style.overflow = 'auto';
}

// Close employee payment QR modal when clicking outside
document.getElementById('emp-payment-qr-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmpPaymentQRModal();
    }
});

// Start Job confirmation function
function confirmStartJob(jobId) {
    Swal.fire({
        title: 'Start Job?',
        text: "Are you really sure you want to start this job?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Start',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit the form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/jobs/${jobId}/start`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
