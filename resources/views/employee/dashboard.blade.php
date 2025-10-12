@extends('layouts.employee')

@section('title','Employee Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
{{-- Employee Dashboard with job assignments and progress tracking --}}
{{-- Purpose: Daily jobs for cleaners, with simple instructions and progress tracking --}}


<div class="max-w-6xl mx-auto px-0 sm:px-0">
	{{-- Mobile: Stacked layout, Desktop: Side by side --}}
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8 gap-4">
		<h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Dashboard</h1>
		<div class="text-sm text-gray-500">
			{{ now()->format('l, F j, Y') }}
		</div>
	</div>

	{{-- Job Statistics Cards - Responsive grid layout --}}
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
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

	{{-- Active Job Assignments - Responsive layout --}}
	<div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
		<div class="p-2 sm:p-4 border-b border-gray-100">
			<h2 class="text-base sm:text-lg font-semibold text-gray-900">Job Assignments</h2>
			<p class="text-xs text-gray-500 mt-1">Your upcoming jobs and current assignments</p>
		</div>
		<div class="p-2 sm:p-4">
			@forelse($todayJobs as $job)
			<div class="border border-gray-200 rounded-lg p-2 sm:p-3 mb-3 last:mb-0 hover:bg-gray-50 transition-colors">
				{{-- Mobile: Stacked layout, Desktop: Side by side --}}
				<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
					<div class="flex-1">
						{{-- Job Header - Mobile: Stacked, Desktop: Side by side --}}
						<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
							<h3 class="text-sm sm:text-base font-semibold text-gray-900">{{ $serviceSummaries[$job->id] ?? ($job->service_name ?? 'General Service') }}</h3>
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
							<span class="inline-block px-2 py-1 text-xs font-medium rounded-full text-center {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
								{{ $job->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $job->status)) }}
							</span>
						</div>
						
						{{-- Job Details - Mobile: Stacked, Desktop: Grid --}}
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
					
					{{-- Action Buttons - Mobile: Full width, Desktop: Column --}}
					<div class="flex flex-col gap-1 sm:ml-3">
						@if($job->status === 'confirmed' || $job->status === 'pending')
							@php
								// Fix timezone issue: Check if job is scheduled for today in local timezone
								// Parse scheduled_start as Manila time and compare with Manila time
								$scheduledDate = \Carbon\Carbon::parse($job->scheduled_start, 'Asia/Manila');
								$today = \Carbon\Carbon::now('Asia/Manila');
								$isScheduledToday = $scheduledDate->isSameDay($today);
								$canStartJob = $isScheduledToday || $job->status === 'in_progress';
							@endphp
							@if($canStartJob)
								<button type="button" onclick="goToJobs()" class="w-full sm:w-auto bg-emerald-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
									Prepare
								</button>
							@else
								<button type="button" disabled class="w-full sm:w-auto bg-gray-400 text-white px-3 py-1.5 rounded text-xs font-medium cursor-not-allowed" title="Job scheduled for {{ \Carbon\Carbon::parse($job->scheduled_start)->format('M j, Y') }}">
									Prepare
								</button>
							@endif
						@endif
						
						{{-- Mobile: Horizontal button row, Desktop: Vertical --}}
						<div class="flex sm:flex-col gap-1">
							@if($paymentSettings && $paymentSettings->qr_code_path && ($job->status === 'pending' || $job->status === 'in_progress'))
							<button onclick="openEmpPaymentQRModal({{ $job->id }}, '{{ $job->code }}', {{ $job->total_due_cents ?? 0 }}, '{{ $job->status }}')" class="flex-1 sm:flex-none bg-purple-600 text-white px-2 sm:px-3 py-1.5 rounded text-xs font-medium hover:bg-purple-700 transition-colors cursor-pointer">
								<i class="ri-qr-code-line mr-1 sm:mr-0"></i>
								<span class="sm:hidden">QR</span>
							</button>
							@endif
							
							<button onclick="openEmpLocation({{ json_encode(['id' => $job->id, 'lat' => $job->latitude, 'lng' => $job->longitude]) }})" class="flex-1 sm:flex-none bg-green-600 text-white px-2 sm:px-3 py-1.5 rounded text-xs font-medium hover:bg-green-700 transition-colors cursor-pointer">
								<i class="ri-map-pin-line mr-1 sm:mr-0"></i>
								<span class="sm:hidden">Map</span>
							</button>
						</div>
						
						<a href="{{ route('employee.jobs') }}" class="text-blue-600 text-xs font-medium hover:text-blue-800 text-center sm:text-left">
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

	{{-- Calendar Section - Responsive layout --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-2 sm:p-6 border-b border-gray-100">
			<h2 class="text-lg sm:text-xl font-semibold text-gray-900">Schedule Calendar</h2>
			<p class="text-sm text-gray-500 mt-1">View your upcoming job assignments</p>
		</div>
		
		{{-- Color Legend - Responsive layout --}}
		<div class="px-2 sm:px-6 py-3 bg-gray-50 border-b border-gray-100">
			{{-- Mobile: Stacked layout, Desktop: Side by side --}}
			<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 text-sm">
				<span class="text-gray-600 font-medium">Status Colors:</span>
				{{-- Mobile: Stacked, Desktop: Side by side --}}
				<div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
					<div class="flex items-center gap-2">
						<div class="w-4 h-4 rounded" style="background-color: #3B82F6;"></div>
						<span class="text-gray-700">Confirmed/Assigned to You</span>
					</div>
					<div class="flex items-center gap-2">
						<div class="w-4 h-4 rounded" style="background-color: #A855F7;"></div>
						<span class="text-gray-700">In Progress</span>
					</div>
					<div class="flex items-center gap-2">
						<div class="w-4 h-4 rounded" style="background-color: #10B981;"></div>
						<span class="text-gray-700">Completed</span>
					</div>
				</div>
			</div>
		</div>
		
		<div class="p-2 sm:p-6">
		<div id="employee-calendar" data-events-url="{{ route('employee.calendar.events') }}"></div>
		</div>
	</div>
</div>

<!-- Location Map Modal - Mobile responsive -->
<div id="job-map-modal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center" style="display: none;">
	<div class="bg-white rounded-xl p-2 sm:p-4 max-w-xl w-full mx-2 sm:mx-4 max-h-[90vh] overflow-y-auto">
		{{-- Mobile: Stacked layout, Desktop: Side by side --}}
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 gap-2">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900">Job Location</h3>
			<button onclick="closeEmpLocation()" class="text-gray-500 hover:text-gray-700 text-xl font-bold cursor-pointer self-end sm:self-center">✕</button>
		</div>
		
		<div class="mb-3">
			<p id="empLocationAddress" class="text-xs sm:text-sm text-gray-700 bg-gray-50 p-2 rounded border mb-2"></p>
			<p id="empLocationPhone" class="text-xs text-gray-500"></p>
		</div>
		
		<div id="jobMap" style="height: 300px; width: 100%;" class="rounded border border-gray-300 bg-gray-100 sm:h-96"></div>
		<div class="flex justify-end gap-2 mt-3">
			<button type="button" onclick="closeEmpLocation()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer text-sm">
				Close
			</button>
		</div>
	</div>
</div>

<!-- Employee Payment QR Code Modal - Mobile responsive -->
<div id="emp-payment-qr-modal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center" style="display: none;">
	<div class="bg-white rounded-lg p-3 sm:p-6 max-w-md w-full mx-2 sm:mx-4">
		{{-- Mobile: Stacked layout, Desktop: Side by side --}}
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900">Payment Information</h3>
			<button onclick="closeEmpPaymentQRModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer self-end sm:self-center">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
		
		<div class="text-center">
			<div class="mb-4">
				<h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Job #<span id="emp-payment-booking-code"></span></h4>
				<p class="text-xl sm:text-2xl font-bold text-emerald-600">₱<span id="emp-payment-amount"></span></p>
				<p class="text-sm text-gray-500 mt-1">Status: <span id="emp-payment-status" class="font-medium"></span></p>
			</div>
			
			@if($paymentSettings && $paymentSettings->qr_code_path)
			<div class="mb-4">
				<img src="{{ Storage::url($paymentSettings->qr_code_path) }}" 
					 alt="GCash QR Code" 
					 class="w-32 h-32 sm:w-48 sm:h-48 object-contain border border-gray-200 rounded-lg mx-auto">
			</div>
			
			<div class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-4">
				<h5 class="font-semibold text-gray-900 mb-2 text-sm sm:text-base">Payment Details</h5>
				<div class="text-xs sm:text-sm text-gray-600 space-y-1">
					<p><span class="font-medium">GCash Name:</span> {{ $paymentSettings->gcash_name }}</p>
					<p><span class="font-medium">GCash Number:</span> {{ $paymentSettings->gcash_number }}</p>
				</div>
			</div>
			
			<div class="bg-blue-50 border border-blue-200 rounded-lg p-2 sm:p-3">
				<div class="flex items-start gap-2">
					<i class="ri-information-line text-blue-500 text-base sm:text-lg mt-0.5"></i>
					<div class="text-xs sm:text-sm text-blue-700">
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
			<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4">
				<div class="flex items-center gap-2">
					<i class="ri-error-warning-line text-yellow-500 text-base sm:text-lg"></i>
					<div class="text-xs sm:text-sm text-yellow-700">
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

// Go to Jobs page function
function goToJobs() {
    window.location.href = '{{ route("employee.jobs") }}';
}

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
            // Submit via AJAX
            submitStartJobViaAjax(jobId);
        }
    });
}

// AJAX submission function for start job action
function submitStartJobViaAjax(jobId) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
    
    // Find and update the start job button to show loading state
    const startButton = document.querySelector(`button[onclick="confirmStartJob(${jobId})"]`);
    let originalButtonContent = '';
    if (startButton) {
        originalButtonContent = startButton.innerHTML;
        startButton.disabled = true;
        startButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1 inline-block"></div>Starting...';
    }
    
    fetch(`/employee/jobs/${jobId}/start`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success alert
            showJobActionSuccessAlert(data.message, 'started');
            
            // Redirect to jobs page to see updated table
            setTimeout(() => {
                window.location.href = '{{ route("employee.jobs") }}';
            }, 1500);
        } else {
            // Handle errors
            showJobActionErrorAlert(data.message || 'An error occurred while starting the job.');
            
            // Reset button
            if (startButton) {
                startButton.disabled = false;
                startButton.innerHTML = originalButtonContent;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showJobActionErrorAlert('An error occurred while starting the job. Please try again.');
        
        // Reset button
        if (startButton) {
            startButton.disabled = false;
            startButton.innerHTML = originalButtonContent;
        }
    });
}

// Show job action success alert that auto-disappears
function showJobActionSuccessAlert(message, action) {
    const alert = document.createElement('div');
    alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.top = '80px'; // Position below the navigation bar
    alert.style.transform = 'translateX(100%)';
    
    const iconClass = action === 'started' ? 'ri-play-line' : 'ri-check-line';
    const actionText = action === 'started' ? 'Job Started' : 'Job Completed';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="${iconClass} text-xl"></i>
            <div>
                <div class="font-medium">${message}</div>
                <div class="text-sm opacity-90">${actionText}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Animate in
    setTimeout(() => {
        alert.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }, 3000);
}

// Show job action error alert
function showJobActionErrorAlert(message) {
    Swal.fire({
        title: 'Action Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK'
    });
}
</script>
@endpush

{{-- Mobile-specific styles for employee dashboard responsiveness - Moved from top --}}
<style>
	/* Mobile responsive styles for employee dashboard */
	@media (max-width: 640px) {
		/* Ensure modals are mobile-friendly */
		#job-map-modal .bg-white,
		#emp-payment-qr-modal .bg-white {
			width: 95vw !important;
			max-width: 95vw !important;
			margin: 0.5rem !important;
		}
		
		/* Make modal content stack vertically on mobile */
		#job-map-modal .flex,
		#emp-payment-qr-modal .flex {
			flex-direction: column !important;
		}
		
		/* Ensure table doesn't cause horizontal overflow */
		.overflow-x-auto {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Make action buttons more touch-friendly */
		.flex .inline-flex {
			min-height: 2.5rem;
		}
		
		/* Reduce card padding and spacing for mobile */
		.block.sm\\:hidden .p-2 {
			padding: 0.375rem !important;
		}
		
		.block.sm\\:hidden .space-y-3 > * + * {
			margin-top: 0.375rem !important;
		}
		
		/* Make the entire card container more compact */
		.block.sm\\:hidden {
			margin-left: -0.25rem !important;
			margin-right: -0.25rem !important;
		}
		
		/* Reduce border radius for more compact look */
		.block.sm\\:hidden .rounded-xl {
			border-radius: 0.375rem !important;
		}
		
		/* Make action buttons much more compact on mobile */
		.block.sm\\:hidden .flex.gap-1 {
			gap: 0.125rem !important;
		}
		
		.block.sm\\:hidden .flex-1 {
			flex: 1 1 0% !important;
			min-width: 0 !important;
		}
		
		/* Make buttons much smaller on mobile */
		.block.sm\\:hidden .px-1 {
			padding-left: 0.25rem !important;
			padding-right: 0.25rem !important;
		}
		
		.block.sm\\:hidden .py-1\\.5 {
			padding-top: 0.25rem !important;
			padding-bottom: 0.25rem !important;
		}
		
		/* Make text much smaller on mobile buttons */
		.block.sm\\:hidden .text-xs {
			font-size: 0.6rem !important;
			line-height: 0.875rem !important;
		}
		
		/* Hide button text on very small screens, show only icons */
		@media (max-width: 480px) {
			.block.sm\\:hidden .text-xs {
				font-size: 0 !important;
				line-height: 0 !important;
			}
			
			.block.sm\\:hidden .mr-0\\.5 {
				margin-right: 0 !important;
			}
			
			.block.sm\\:hidden .px-1 {
				padding-left: 0.25rem !important;
				padding-right: 0.25rem !important;
			}
			
			.block.sm\\:hidden .py-1 {
				padding-top: 0.125rem !important;
				padding-bottom: 0.125rem !important;
			}
		}
		
		/* Extra small screens - make buttons even more compact */
		@media (max-width: 360px) {
			.block.sm\\:hidden .flex.gap-0\\.5 {
				gap: 0.0625rem !important;
			}
			
			.block.sm\\:hidden .px-1 {
				padding-left: 0.125rem !important;
				padding-right: 0.125rem !important;
			}
		}
		
		/* Reduce grid gap on mobile */
		.block.sm\\:hidden .grid.gap-4 {
			gap: 0.5rem !important;
		}
		
		/* Make text smaller on mobile for more compact cards */
		.block.sm\\:hidden .text-sm {
			font-size: 0.8rem !important;
			line-height: 1.125rem !important;
		}
		
		.block.sm\\:hidden .text-xs {
			font-size: 0.7rem !important;
			line-height: 1rem !important;
		}
		
		/* Calendar responsive styles */
		#employee-calendar {
			width: 100% !important;
			max-width: 100% !important;
			overflow-x: hidden !important;
		}
		
		.fc-header-toolbar {
			flex-wrap: wrap !important;
			gap: 0.5rem !important;
		}
		
		.fc-button {
			font-size: 0.75rem !important;
			padding: 0.25rem 0.5rem !important;
		}
		
		.fc-toolbar-title {
			font-size: 1rem !important;
			white-space: nowrap !important;
			overflow: hidden !important;
			text-overflow: ellipsis !important;
		}
	}
</style>
