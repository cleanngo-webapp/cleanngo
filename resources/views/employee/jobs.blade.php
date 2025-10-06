@extends('layouts.employee')

@section('title','My Jobs')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">My Jobs</h1>

    {{-- Job Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-6">
        {{-- Active Jobs Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Assigned Jobs</p>
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

        {{-- Completed Jobs Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed Jobs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($jobsCompletedOverall) }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time completed</p>
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

    {{-- Search and Sort Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-jobs" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search jobs by Booking ID, Customer Name, or Status" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'date') === 'date' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('date')">
                        <i class="ri-calendar-line mr-2"></i>
                        Sort by Date
                        <i class="ri-arrow-{{ ($sort ?? 'date') === 'date' && ($sortOrder ?? 'desc') === 'desc' ? 'down' : 'up' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'date') === 'customer' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('customer')">
                        <i class="ri-user-line mr-2"></i>
                        Sort by Customer
                        <i class="ri-arrow-{{ ($sort ?? 'date') === 'customer' && ($sortOrder ?? 'desc') === 'desc' ? 'down' : 'up' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- My Jobs Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">My Job Assignments</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage your assigned jobs and track progress</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto lg:overflow-x-visible">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Booking ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[300px]">Actions</th>
                    </tr>
                </thead>
                <tbody id="jobs-table-body" class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors" data-booking-id="{{ $b->id }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y g:i A') : '—' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $b->customer_name ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
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
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$b->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $b->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $b->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1 flex-wrap">
                                @if($b->status === 'in_progress')
                                    @if($b->payment_approved)
                                        <button type="button" onclick="confirmCompleteJob({{ $b->id }}, '{{ $b->code }}')" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors cursor-pointer" title="Mark as complete">
                                            <i class="ri-check-line mr-1"></i>
                                            <span class="hidden sm:inline">Complete</span>
                                        </button>
                                    @else
                                        <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof required" disabled>
                                            <i class="ri-check-line mr-1"></i>
                                            <span class="hidden sm:inline">Complete</span>
                                        </button>
                                    @endif
                                    @if($b->payment_status === 'declined' || !$b->payment_proof_id)
                                        <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openPaymentModal({{ $b->id }})" title="Attach Payment Proof">
                                            <i class="ri-attachment-line mr-1"></i>
                                            <span class="hidden sm:inline">Attachments</span>
                                        </button>
                                    @else
                                        <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof already uploaded - waiting for admin review" disabled>
                                            <i class="ri-attachment-line mr-1"></i>
                                            <span class="hidden sm:inline">Attachments</span>
                                        </button>
                                    @endif
                                @elseif($b->status === 'pending' || $b->status === 'confirmed')
                                    @php
                                        // Fix timezone issue: Check if job is scheduled for today in local timezone
                                        // Parse scheduled_start as Manila time and compare with Manila time
                                        $scheduledDate = \Carbon\Carbon::parse($b->scheduled_start, 'Asia/Manila');
                                        $today = \Carbon\Carbon::now('Asia/Manila');
                                        $isScheduledToday = $scheduledDate->isSameDay($today);
                                        $canStartJob = $isScheduledToday || $b->status === 'in_progress';
                                    @endphp
                                        @if($canStartJob)
                                            @if($b->equipment_borrowed == 1)
                                                <!-- Equipment already borrowed - show Items and Start Job buttons -->
                                                <button type="button" onclick="getEquipment({{ $b->id }})" class="hidden" title="Get Equipment">
                                                    <i class="ri-tools-line mr-1"></i>
                                                    <span class="hidden sm:inline">Get Equipment</span>
                                                </button>
                                                <button type="button" onclick="openBorrowedItemsModal({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-purple-300 shadow-sm text-xs font-medium rounded text-purple-600 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-colors cursor-pointer" title="View Borrowed Items">
                                                    <i class="ri-list-check mr-1"></i>
                                                    <span class="hidden sm:inline">Items</span>
                                                </button>
                                                <button type="button" onclick="confirmStartJob({{ $b->id }})" id="start-job-btn-{{ $b->id }}" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer" title="Start Job">
                                                    <i class="ri-play-line mr-1"></i>
                                                    <span class="hidden sm:inline">Start Job</span>
                                                </button>
                                            @else
                                                <!-- No equipment borrowed yet - show Get Equipment button -->
                                                <button type="button" onclick="getEquipment({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer" title="Get Equipment">
                                                    <i class="ri-tools-line mr-1"></i>
                                                    <span class="hidden sm:inline">Get Equipment</span>
                                                </button>
                                                <!-- Start Job button - hidden until equipment is borrowed -->
                                            @endif
                                        @else
                                            <button type="button" disabled class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Job scheduled for {{ \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y') }}">
                                                <i class="ri-play-line mr-1"></i>
                                                <span class="hidden sm:inline">Start Job</span>
                                            </button>
                                        @endif
                                @endif
                                <button type="button" class="inline-flex items-center px-2 py-1 border border-emerald-300 shadow-sm text-xs font-medium rounded text-emerald-600 hover:bg-emerald-50 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpReceipt({{ $b->id }})" title="View Service Summary">
                                    <i class="ri-receipt-line mr-1"></i>
                                    <span class="hidden sm:inline">Summary</span>
                                </button>
                                <button type="button" class="inline-flex items-center px-2 py-1 border border-emerald-300 shadow-sm text-xs font-medium rounded text-emerald-600 hover:bg-emerald-50 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpLocation({ id: {{ $b->id }}, lat: {{ $b->latitude ?? 0 }}, lng: {{ $b->longitude ?? 0 }} })" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    <span class="hidden sm:inline">Location</span>
                                </button>
                                @php
                                    $bookingPhotos = $b->booking_photos ? json_decode($b->booking_photos, true) : [];
                                    $hasPhotos = is_array($bookingPhotos) && count($bookingPhotos) > 0;
                                @endphp
                                @if($hasPhotos)
                                    <button type="button" class="inline-flex items-center px-2 py-1 border border-emerald-300 shadow-sm text-xs font-medium rounded text-emerald-600 hover:bg-emerald-50 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpBookingPhotos({{ $b->id }})" title="View Booking Photos">
                                        <i class="ri-image-line mr-1"></i>
                                        <span class="hidden sm:inline">Photos</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                                    <i class="ri-briefcase-3-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Job Assignments Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No job assignments match your current filters. Try adjusting your search criteria.
                                        @else
                                            You don't have any job assignments yet. Jobs will appear here once the admin assigns them to you.
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('sort'))
                                            <button onclick="clearFilters()" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                                <i class="ri-refresh-line mr-2"></i>
                                                Clear Filters
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    </div>
    <div id="job-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-xl p-4 m-4">
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-lg">Customer Location</div>
                <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="const m=document.getElementById('job-map-modal'); m.classList.add('hidden'); m.classList.remove('flex');">✕</button>
            </div>
            <div id="empLocationAddress" class="text-sm mb-3 text-gray-700 bg-gray-50 p-2 rounded border"></div>
            <div id="empLocationPhone" class="text-xs mb-3 text-gray-500"></div>
            <div id="jobMap" class="h-80 rounded border border-gray-300 bg-gray-100"></div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" onclick="const m=document.getElementById('job-map-modal'); m.classList.add('hidden'); m.classList.remove('flex');" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
    <!-- Payment Proof Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Attach Proof of Payment</div>
                <button class="cursor-pointer" onclick="closePaymentModal()">✕</button>
            </div>
            <form id="payment-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500 cursor-pointer" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof Image</label>
                    
                    <!-- Custom File Input Container -->
                    <div class="relative">
                        <input type="file" name="proof_image" accept="image/*" id="proof-image-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                        <div id="file-input-display" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white hover:bg-gray-50 transition-colors cursor-pointer">
                            <span id="file-input-text" class="text-gray-500">Choose File</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Upload Receipt or Cash in hand image (max 2MB)</p>
                    
                    <!-- Image Preview Container -->
                    <div id="image-preview-container" class="mt-3 hidden">
                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                            <p class="text-sm font-medium text-gray-700 mb-2">Image Preview:</p>
                            <div class="relative">
                                <img id="image-preview" src="" alt="Payment proof preview" class="w-full h-48 object-contain rounded bg-white">
                                <button type="button" id="remove-image-preview" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors cursor-pointer" title="Remove image">
                                    ✕
                                </button>
                            </div>
                            <p id="image-info" class="text-xs text-gray-500 mt-2"></p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closePaymentModal()">Cancel</button>
                    <button type="button" onclick="confirmAttachPayment()" class="px-3 py-2 bg-purple-600 text-white rounded cursor-pointer hover:bg-purple-700">Attach Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipt Modal Component -->
    @include('components.receipt-modal', [
        'modalId' => 'emp-receipt-modal',
        'receiptData' => $receiptData ?? [],
        'bookingId' => null
    ])

    <!-- Booking Photos Modal -->
    <div id="emp-booking-photos-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-4xl p-4 m-4" style="max-height: 90vh; overflow-y: auto;">
            <div class="flex items-center justify-between mb-4">
                <div class="font-semibold text-lg">Booking Photos</div>
                <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeEmpBookingPhotosModal()">✕</button>
            </div>
            <div id="emp-booking-photos-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
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
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const addr = document.getElementById('empLocationAddress');
    const phone = document.getElementById('empLocationPhone');
    // find address and phone from server-provided locationsData if available
    try {
        const data = (window.empLocations && (window.empLocations[payload.id] || window.empLocations[String(payload.id)])) || null;
        if (data) {
            addr.textContent = data.address || '';
            phone.textContent = data.phone ? ('Contact: ' + data.phone) : '';
        } else {
            addr.textContent = '';
            phone.textContent = '';
        }
    } catch(e){ addr.textContent=''; phone.textContent=''; }
    setTimeout(function(){
        if(!jobMap){
            jobMap = L.map('jobMap').setView([lat,lng], (lat&&lng)?15:5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(jobMap);
        } else { jobMap.setView([lat,lng], (lat&&lng)?15:5); }
        if(!jobMarker){ jobMarker = L.marker([lat,lng]).addTo(jobMap); } else { jobMarker.setLatLng([lat,lng]); }
        setTimeout(()=>{ if(jobMap) jobMap.invalidateSize(true); }, 100);
    }, 50);
}
// Make locations available globally for address/phone rendering
window.empLocations = @json($locationsData ?? []);
const empReceipts = @json($receiptData ?? []);
// Receipt functions now handled by the component
function openEmpReceipt(id){
    openReceipt('emp-receipt-modal', id, empReceipts);
}

// Payment modal functions
let currentBookingId = null;
function openPaymentModal(bookingId) {
    currentBookingId = bookingId;
    const modal = document.getElementById('payment-modal');
    const form = document.getElementById('payment-form');
    form.action = `/employee/payment-proof/${bookingId}/upload`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentBookingId = null;
    // Reset form
    document.getElementById('payment-form').reset();
    // Hide image preview
    hideImagePreview();
}

// Image preview functionality
function showImagePreview(file) {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const imageInfo = document.getElementById('image-info');
    
    // Validate file size (2MB limit)
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if (file.size > maxSize) {
        alert('File size must be less than 2MB');
        document.getElementById('proof-image-input').value = '';
        return;
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file');
        document.getElementById('proof-image-input').value = '';
        return;
    }
    
    // Create file reader to display image
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        imageInfo.textContent = `File: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        previewContainer.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function hideImagePreview() {
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const imageInfo = document.getElementById('image-info');
    
    previewContainer.classList.add('hidden');
    previewImage.src = '';
    imageInfo.textContent = '';
}

// Add event listeners for image preview
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('proof-image-input');
    const removePreviewBtn = document.getElementById('remove-image-preview');
    const fileInputText = document.getElementById('file-input-text');
    
    // Handle file input change
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Update the custom file input text to show selected file name
                fileInputText.textContent = file.name;
                fileInputText.className = 'text-gray-700 font-medium';
                showImagePreview(file);
            } else {
                // Reset to default text when no file is selected
                fileInputText.textContent = 'Choose File';
                fileInputText.className = 'text-gray-500';
                hideImagePreview();
            }
        });
    }
    
    // Handle remove preview button
    if (removePreviewBtn) {
        removePreviewBtn.addEventListener('click', function() {
            document.getElementById('proof-image-input').value = '';
            // Reset the custom file input text
            fileInputText.textContent = 'Choose File';
            fileInputText.className = 'text-gray-500';
            hideImagePreview();
        });
    }
});

// Global variables for search and sort
let currentSort = '{{ $sort ?? "date" }}';
let currentSortOrder = '{{ $sortOrder ?? "desc" }}';
let searchTimeout;

// Search and sort functionality
function toggleSort(sortType) {
    if (currentSort === sortType) {
        // Toggle sort order if same sort type
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        // Set new sort type with default ascending order
        currentSort = sortType;
        currentSortOrder = 'asc';
    }
    
    // Update button styles and icons
    updateSortButtons();
    
    // Perform search/sort
    performSearch();
}

function updateSortButtons() {
    const buttons = document.querySelectorAll('[onclick^="toggleSort"]');
    buttons.forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
        
        // Update arrow icons
        const icon = btn.querySelector('i:last-child');
        if (btn.onclick.toString().includes(currentSort)) {
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-emerald-600', 'text-white');
            icon.className = `ri-arrow-${currentSortOrder === 'desc' ? 'down' : 'up'}-line ml-2`;
        } else {
            icon.className = 'ri-arrow-up-line ml-2';
        }
    });
}

// Auto-search on input (with debounce)
document.getElementById('search-jobs').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 300); // 300ms delay for faster response
});

// AJAX search function
function performSearch() {
    const searchTerm = document.getElementById('search-jobs').value;
    const url = new URL('{{ route("employee.jobs") }}', window.location.origin);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    url.searchParams.set('sort', currentSort);
    url.searchParams.set('sort_order', currentSortOrder);
    
    // Show loading state
    const tableBody = document.getElementById('jobs-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Searching...</td></tr>';
    paginationContainer.innerHTML = '';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.getElementById('jobs-table-body');
            const newPagination = doc.getElementById('pagination-container');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            if (newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
        });
}

// Clear all filters function
function clearFilters() {
    // Clear search input
    const searchInput = document.getElementById('search-jobs');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset sort
    currentSort = 'date';
    currentSortOrder = 'desc';
    updateSortButtons();
    
    // Perform search to refresh results
    performSearch();
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

// Attach Payment confirmation function
function confirmAttachPayment() {
    // Get form data for validation
    const form = document.getElementById('payment-form');
    const amount = form.querySelector('input[name="amount"]').value;
    const paymentMethod = form.querySelector('select[name="payment_method"]').value;
    const proofImage = form.querySelector('input[name="proof_image"]').files[0];
    
    // Validate required fields
    if (!amount || !paymentMethod || !proofImage) {
        Swal.fire({
            title: 'Missing Information',
            text: 'Please fill in all required fields: Amount, Payment Method, and Payment Proof Image.',
            icon: 'warning',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    // Show confirmation modal with details
    Swal.fire({
        title: 'Attach Payment Proof?',
        html: `
            <div class="text-left">
                <p class="mb-2"><strong>Are you sure you want to attach this payment proof?</strong></p>
                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    <p><strong>Amount:</strong> ₱${parseFloat(amount).toFixed(2)}</p>
                    <p><strong>Payment Method:</strong> ${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</p>
                    <p><strong>Proof Image:</strong> ${proofImage.name}</p>
                </div>
                <p class="mt-2 text-sm text-gray-600">Please verify all details are correct before proceeding.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Attach Payment',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form via AJAX
            submitPaymentProofViaAjax(form);
        }
    });
}

// Submit payment proof via AJAX and handle response
function submitPaymentProofViaAjax(form) {
    const formData = new FormData(form);
    const submitButton = document.querySelector('button[onclick="confirmAttachPayment()"]');
    
    // Show loading state on the button with enhanced preloader
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-75', 'cursor-not-allowed');
        submitButton.innerHTML = `
            <div class="flex items-center justify-center">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                <span>Attaching Payment...</span>
            </div>
        `;
    }
    
    // Also disable the Attachments button in the main table to prevent multiple clicks
    const attachmentsButton = document.querySelector(`button[onclick="openPaymentModal(${currentBookingId})"]`);
    if (attachmentsButton) {
        attachmentsButton.disabled = true;
        attachmentsButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success alert that auto-disappears
            showPaymentSuccessAlert(data.message);
            
            // Close modal and reset form
            closePaymentModal();
            
            // Update the table via AJAX instead of page reload
            setTimeout(() => {
                refreshJobTable();
            }, 1500);
            
            // Note: No need to reset button states here as modal closes and table refreshes
        } else {
            // Handle validation errors
            showPaymentErrorAlert(data.message || 'An error occurred while uploading the payment proof.');
            
            // Reset button states
            resetPaymentButtonStates(submitButton, attachmentsButton);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPaymentErrorAlert('An error occurred while uploading the payment proof. Please try again.');
        
        // Reset button states
        resetPaymentButtonStates(submitButton, attachmentsButton);
    })
    .finally(() => {
        // This will be handled by resetPaymentButtonStates in success/error cases
    });
}

// Helper function to reset payment button states after error
function resetPaymentButtonStates(submitButton, attachmentsButton) {
    // Reset submit button
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
        submitButton.innerHTML = 'Attach Payment';
    }
    
    // Reset attachments button if it exists
    if (attachmentsButton) {
        attachmentsButton.disabled = false;
        attachmentsButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Show payment success alert that auto-disappears
function showPaymentSuccessAlert(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.top = '80px'; // Position below the navigation bar
    alert.style.transform = 'translateX(100%)';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-check-line text-xl"></i>
            <div>
                <div class="font-medium">${message}</div>
                <div class="text-sm opacity-90">Waiting for admin approval</div>
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

// Show payment error alert
function showPaymentErrorAlert(message) {
    Swal.fire({
        title: 'Upload Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK'
    });
}

// Complete Job confirmation function
function confirmCompleteJob(jobId, jobCode) {
    Swal.fire({
        title: 'Complete Job?',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Are you sure you want to mark this job as complete?</strong></p>
                <div class="bg-gray-50 p-3 rounded-lg text-sm mb-3">
                    <p><strong>Job ID:</strong> ${jobCode}</p>
                    <p><strong>Status:</strong> Payment Approved ✓</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg text-sm">
                    <p class="text-yellow-800"><strong>⚠️ Important:</strong></p>
                    <ul class="text-yellow-700 mt-1 space-y-1">
                        <li>• Ensure all cleaning tasks are completed</li>
                        <li>• Verify customer satisfaction</li>
                        <li>• Confirm payment has been received</li>
                        <li>• This action cannot be undone</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Mark Complete',
        cancelButtonText: 'Cancel',
        focusCancel: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit via AJAX
            submitCompleteJobViaAjax(jobId, jobCode);
        }
    });
}

// AJAX submission functions for job actions
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
            
            // Update the table via AJAX instead of page reload
            setTimeout(() => {
                refreshJobTable();
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

function submitCompleteJobViaAjax(jobId, jobCode) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
    
    // Find and update the complete job button to show loading state
    const completeButton = document.querySelector(`button[onclick="confirmCompleteJob(${jobId}, '${jobCode}')"]`);
    let originalButtonContent = '';
    if (completeButton) {
        originalButtonContent = completeButton.innerHTML;
        completeButton.disabled = true;
        completeButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1 inline-block"></div>Completing...';
    }
    
    fetch(`/employee/jobs/${jobId}/complete`, {
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
            showJobActionSuccessAlert(data.message, 'completed');
            
            // Update the table via AJAX instead of page reload
            setTimeout(() => {
                refreshJobTable();
            }, 1500);
        } else {
            // Handle errors
            showJobActionErrorAlert(data.message || 'An error occurred while completing the job.');
            
            // Reset button
            if (completeButton) {
                completeButton.disabled = false;
                completeButton.innerHTML = originalButtonContent;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showJobActionErrorAlert('An error occurred while completing the job. Please try again.');
        
        // Reset button
        if (completeButton) {
            completeButton.disabled = false;
            completeButton.innerHTML = originalButtonContent;
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

// Employee Booking Photos Modal Functions
let currentEmpBookingPhotos = null;

function openEmpBookingPhotos(bookingId) {
    currentEmpBookingPhotos = bookingId;
    const modal = document.getElementById('emp-booking-photos-modal');
    const content = document.getElementById('emp-booking-photos-content');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="flex justify-center items-center space-x-2 mb-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
            </div>
            <p class="text-gray-500 text-sm">Loading booking photos...</p>
        </div>
    `;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch booking photos
    fetch(`/employee/bookings/${bookingId}/photos`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.photos && data.photos.length > 0) {
                let photosHtml = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
                
                data.photos.forEach((photo, index) => {
                    photosHtml += `
                        <div class="relative group">
                            <img src="${photo.url}" alt="Booking Photo ${index + 1}" 
                                 class="w-full h-64 object-cover rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer"
                                 data-photo-url="${photo.url}" 
                                 data-photo-filename="${photo.filename}">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center pointer-events-none">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="ri-zoom-in-line text-white text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <p class="text-sm text-gray-600">${photo.filename}</p>
                            </div>
                        </div>
                    `;
                });
                
                photosHtml += '</div>';
                content.innerHTML = photosHtml;
                
                // Add event delegation for photo clicks
                content.addEventListener('click', function(e) {
                    if (e.target.tagName === 'IMG' && e.target.dataset.photoUrl) {
                        openEmpPhotoViewer(e.target.dataset.photoUrl, e.target.dataset.photoFilename);
                    }
                });
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-image-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Photos Available</h3>
                        <p class="text-sm text-gray-500">This booking doesn't have any photos uploaded.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading booking photos:', error);
            content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading booking photos.</div>';
        });
}

function closeEmpBookingPhotosModal() {
    const modal = document.getElementById('emp-booking-photos-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentEmpBookingPhotos = null;
}

function openEmpPhotoViewer(imageUrl, filename) {
    // Create a simple photo viewer modal
    const viewer = document.createElement('div');
    viewer.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[2000]';
    viewer.innerHTML = `
        <div class="relative max-w-4xl max-h-full p-4">
            <button class="emp-photo-viewer-close absolute top-4 right-4 text-white text-2xl hover:text-gray-300 cursor-pointer z-10">
                ✕
            </button>
            <img src="${imageUrl}" alt="${filename}" class="max-w-full max-h-full object-contain">
            <div class="absolute bottom-4 left-4 text-white text-sm bg-black bg-opacity-50 px-2 py-1 rounded">
                ${filename}
            </div>
        </div>
    `;
    document.body.appendChild(viewer);
    
    // Add event listener for close button
    const closeBtn = viewer.querySelector('.emp-photo-viewer-close');
    closeBtn.addEventListener('click', () => {
        viewer.remove();
    });
    
    // Close on click outside image
    viewer.addEventListener('click', (e) => {
        if (e.target === viewer) {
            viewer.remove();
        }
    });
    
    // Close on Escape key
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            viewer.remove();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}

// Equipment Modal Section
function getEquipment(bookingId) {
    const modal = document.getElementById('equipment-modal');
    const content = document.getElementById('equipment-content');
    const bookingIdField = document.getElementById('equipment-booking-id');
    
    // Set the booking ID
    bookingIdField.value = bookingId;
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="flex justify-center items-center space-x-2 mb-4">
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
            </div>
            <p class="text-gray-500 text-sm">Loading available equipment...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch available inventory items
    fetch('/employee/inventory/available')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                let equipmentHtml = '<div class="space-y-4 relative equipment-content">';
                equipmentHtml += '<p class="text-sm text-gray-600 mb-4">Select equipment you need for this job. Items marked as returnable will be automatically returned when the job is completed.</p>';
                
                data.items.forEach(item => {
                    const isReturnable = item.is_returnable;
                    const badgeClass = isReturnable ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
                    const badgeText = isReturnable ? 'Returnable' : 'Consumable';
                    
                    equipmentHtml += `
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow bg-white relative z-10 equipment-item">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h4 class="font-medium text-gray-900">${item.name}</h4>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                                            ${badgeText}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="font-medium">${item.category}</span> • Code: ${item.item_code}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Available: <span class="font-medium text-green-600">${item.available_quantity}</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="number" 
                                           class="equipment-quantity w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           data-item-id="${item.id}"
                                           data-max="${item.available_quantity}"
                                           min="0" 
                                           max="${item.available_quantity}"
                                           oninput="validateEquipmentQuantity(this)">
                                    <button type="button" 
                                            onclick="addEquipmentToList(${item.id}, '${item.name}', '${item.category}', '${item.item_code}', ${isReturnable})"
                                            class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded border border-blue-200 transition-colors">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                equipmentHtml += '</div>';
                
                // Add selected equipment list area
                equipmentHtml += `
                    <div class="mt-6 pt-6 border-t border-gray-200 bg-white relative z-10 selected-equipment-section">
                        <h4 class="font-medium text-gray-900 mb-4">Selected Equipment</h4>
                        <div id="selected-equipment-list" class="space-y-2">
                            <p class="text-sm text-gray-500 italic">No equipment selected yet.</p>
                        </div>
                    </div>
                `;
                
                content.innerHTML = equipmentHtml;
                
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-tools-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Equipment Available</h3>
                        <p class="text-sm text-gray-500">No equipment is available for borrowing at the moment.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading equipment:', error);
            content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading available equipment.</div>';
        });
}

let selectedEquipmentList = [];

function validateEquipmentQuantity(input) {
    const maxAvailable = parseInt(input.getAttribute('data-max')) || 0;
    const currentValue = parseInt(input.value) || 0;
    const itemId = input.getAttribute('data-item-id');
    
    // Find if this item is already selected
    const existingItem = selectedEquipmentList.find(item => item.id == itemId);
    const alreadySelected = existingItem ? existingItem.quantity : 0;
    const totalRequested = alreadySelected + currentValue;
    
    // Reset styling  
    input.classList.remove('border-red-500', 'bg-red-50', 'border-yellow-500', 'bg-yellow-50');
    
    // Validate against stock
    if (currentValue > maxAvailable) {
        input.classList.remove('border-gray-300', 'bg-white', 'border-yellow-500', 'bg-yellow-50');
        input.classList.add('border-red-500', 'bg-red-50');
        input.title = `⚠️ Cannot exceed available stock: ${maxAvailable} units`;
    } else if (totalRequested > maxAvailable && existingItem) {
        input.classList.remove('border-gray-300', 'bg-white', 'border-yellow-500', 'bg-yellow-50');
        input.classList.add('border-red-500', 'bg-red-50');
        input.title = `⚠️ Total would exceed stock! Max you can add: ${maxAvailable - alreadySelected} units`;
    } else if (currentValue > maxAvailable * 0.8) {
        // Warning when approaching 80% of available stock
        input.classList.remove('border-gray-300', 'bg-white', 'border-red-500', 'bg-red-50');
        input.classList.add('border-yellow-500', 'bg-yellow-50');
        input.title = `⚠️ High quantity requested! Available: ${maxAvailable} units`;
    } else {
        input.classList.remove('border-red-500', 'bg-red-50', 'border-yellow-500', 'bg-yellow-50');
        input.classList.add('border-gray-300', 'bg-white');
        if (currentValue > 0) {
            input.title = `Available: ${maxAvailable} units`;
        } else {
            input.title = '';
        }
    }
}

function addEquipmentToList(itemId, itemName, category, itemCode, isReturnable) {
    const quantityInput = document.querySelector(`input[data-item-id="${itemId}"]`);
    const quantity = parseInt(quantityInput.value) || 0;
    const maxAvailable = parseInt(quantityInput.getAttribute('data-max')) || 0;
    
    // Validation: Check if quantity is valid
    if (quantity <= 0) {
        Swal.fire({
            title: 'Invalid Quantity',
            text: 'Please enter a valid quantity (greater than 0).',
            icon: 'warning',
           confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Validation: Check if quantity exceeds available amount
    if (quantity > maxAvailable) {
        Swal.fire({
            title: 'Insufficient Stock',
            html: `
                <div class="text-left">
                    <p class="mb-3"><strong>Cannot add more than available stock!</strong></p>
                    <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700">Equipment:</span>
                            <span class="text-gray-900">${itemName}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700">You requested:</span>
                            <span class="text-red-600 font-bold">${quantity}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700">Available stock:</span>
                            <span class="text-green-600 font-bold">${maxAvailable}</span>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-gray-600">Please reduce the quantity to ${maxAvailable} or less.</p>
                </div>
            `,
            icon: 'error',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Check if item is already selected and would exceed total available
    const existingItem = selectedEquipmentList.find(item => item.id === itemId);
    if (existingItem) {
        const totalQuantity = existingItem.quantity + quantity;
        if (totalQuantity > maxAvailable) {
            Swal.fire({
                title: 'Total Quantity Exceeds Stock',
                html: `
                    <div class="text-left">
                        <p class="mb-3"><strong>Cannot add more than available stock!</strong></p>
                        <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Equipment:</span>
                                <span class="text-gray-900">${itemName}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Already selected:</span>
                                <span class="text-blue-600 font-bold">${existingItem.quantity}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">New request:</span>
                                <span class="text-blue-600 font-bold">${quantity}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Total would be:</span>
                                <span class="text-red-600 font-bold">${totalQuantity}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Available stock:</span>
                                <span class="text-green-600 font-bold">${maxAvailable}</span>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-600">Maximum you can add: <strong>${maxAvailable - existingItem.quantity}</strong></p>
                    </div>
                `,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'OK'
            });
            return;
        }
        existingItem.quantity += quantity;
    } else {
        selectedEquipmentList.push({
            id: itemId,
            name: itemName,
            category: category,
            item_code: itemCode,
            quantity: quantity,
            is_returnable: isReturnable
        });
    }
    
    // Reset quantity input
    quantityInput.value = '';
    
    // Update selected equipment list display
    updateSelectedEquipmentList();
}

function updateSelectedEquipmentList() {
    const listContainer = document.getElementById('selected-equipment-list');
    
    if (selectedEquipmentList.length === 0) {
        listContainer.innerHTML = '<p class="text-sm text-gray-500 italic">No equipment selected yet.</p>';
        return;
    }
    
    let listHtml = '';
    selectedEquipmentList.forEach((item, index) => {
        const badgeClass = item.is_returnable ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
        const badgeText = item.is_returnable ? 'Returnable' : 'Consumable';
        
        listHtml += `
            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-900">${item.name}</span>
                    <span class="text-xs text-gray-500">${item.category}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                        ${badgeText}
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900">Quantity: ${item.quantity}</span>
                    <button type="button" onclick="removeEquipmentFromList(${index})" class="text-red-500 hover:text-red-700">
                        <i class="ri-close-line"></i>

</button>
                </div>
            </div>
        `;
    });
    
    listContainer.innerHTML = listHtml;
}

function removeEquipmentFromList(index) {
    selectedEquipmentList.splice(index, 1);
    updateSelectedEquipmentList();
}

function confirmGetEquipment() {
    if (selectedEquipmentList.length === 0) {
        Swal.fire({
            title: 'No Equipment Selected',
            text: 'Please select at least one equipment item to proceed.',
            icon: 'warning',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show confirmation dialog with equipment details
    let confirmationHtml = '<div class="text-left"><p class="mb-3"><strong>Are you sure you want to borrow this equipment?</strong></p><div class="bg-gray-50 p-4 rounded-lg text-sm space-y-2">';
    
    selectedEquipmentList.forEach(item => {
        const badgeClass = item.is_returnable ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
        const badgeText = item.is_returnable ? 'Returnable' : 'Consumable';
        
        confirmationHtml += `
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-medium text-gray-700">${item.name}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2 ${badgeClass}">
                        ${badgeText}
                    </span>
                </div>
                <span class="text-gray-900 ">Qty: ${item.quantity}</span>
            </div>
        `;
        });
        
        confirmationHtml += '</div><p class="mt-3 text-sm text-gray-600">Returnable items will be automatically returned when the job is completed.</p></div>';
    
    Swal.fire({
        title: 'Confirm Equipment Borrowing?',
        html: confirmationHtml,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Borrow Equipment',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            submitEquipmentRequest();
        }
    });
}

function submitEquipmentRequest() {
    const bookingId = document.getElementById('equipment-booking-id').value;
    
    // Prepare clean equipment data with only required fields
    const cleanEquipmentData = selectedEquipmentList.map(item => ({
        id: parseInt(item.id),
        quantity: parseInt(item.quantity)
    }));
    
    console.log('Sending equipment data:', cleanEquipmentData); // Debug log
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
    formData.append('equipment', JSON.stringify(cleanEquipmentData));
    
    // Show enhanced loading state on submit button with preloader
    const submitButton = document.querySelector('#equipment-form .bg-blue-600');
    const originalButtonContent = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.classList.add('opacity-75', 'cursor-not-allowed');
    submitButton.innerHTML = `
        <div class="flex items-center justify-center">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
            <span>Processing...</span>
        </div>
    `;
    
    // Also disable the Get Equipment button in the main table to prevent multiple clicks
    const getEquipmentButton = document.querySelector(`button[onclick="getEquipment(${bookingId})"]`);
    if (getEquipmentButton) {
        getEquipmentButton.disabled = true;
        getEquipmentButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    fetch(`/employee/jobs/${bookingId}/equipment/borrow`, {
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
            showEquipmentSuccessAlert(data.message);
            
            // Close modal and reset
            closeEquipmentModal();
            
            // Update the table via AJAX instead of page reload
            setTimeout(() => {
                refreshJobTable();
            }, 1500);
        } else {
            // Handle validation errors with specific details
            let errorMessage = 'An error occurred while borrowing equipment.';
            
            if (data.message) {
                errorMessage = data.message;
            } else if (data.errors) {
                const errorList = Object.values(data.errors).flat();
                errorMessage = errorList.join('\n');
            }
            
            showEquipmentErrorAlert(errorMessage);
            
            // Reset button states
            resetButtonStates(submitButton, originalButtonContent, getEquipmentButton);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showEquipmentErrorAlert('An error occurred while borrowing equipment. Please try again.');
        
        // Reset button states
        resetButtonStates(submitButton, originalButtonContent, getEquipmentButton);
    });
}

function closeEquipmentModal() {
    const modal = document.getElementById('equipment-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Reset selected equipment
    selectedEquipmentList = [];
}

function showEquipmentSuccessAlert(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.top = '80px';
    alert.style.transform = 'translateX(100%)';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-check-line text-xl"></i>
            <div>
                <div class="font-medium">${message}</div>
                <div class="text-sm opacity-90">Equipment Borrowed Successfully</div>
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

function showEquipmentErrorAlert(message) {
    Swal.fire({
        title: 'Equipment Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK'
    });
}

// Helper function to reset button states after error
function resetButtonStates(submitButton, originalButtonContent, getEquipmentButton) {
    // Reset submit button
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
    submitButton.innerHTML = originalButtonContent;
    
    // Reset get equipment button if it exists
    if (getEquipmentButton) {
        getEquipmentButton.disabled = false;
        getEquipmentButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Function to refresh the entire job table via AJAX
function refreshJobTable() {
    // Show loading indicator on the table
    const tableBody = document.getElementById('jobs-table-body');
    const originalContent = tableBody.innerHTML;
    
    // Add loading overlay to the table
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-gray-500 text-sm">Updating job assignments...</span>
                </div>
            </td>
        </tr>
    `;
    
    // Fetch updated table data
    fetch('/employee/jobs/table-data', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the table body with new data
            tableBody.innerHTML = data.tableHtml;
            
            // Update statistics cards
            updateStatisticsCards(data.statistics);
        } else {
            // If AJAX fails, fallback to page reload
            console.warn('Failed to refresh table via AJAX, reloading page');
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error refreshing table:', error);
        // If AJAX fails, fallback to page reload
        window.location.reload();
    });
}

// Function to update statistics cards
function updateStatisticsCards(statistics) {
    // Update Assigned Jobs card
    const assignedJobsElement = document.querySelector('.bg-white.rounded-xl.p-6.shadow-sm.border.border-gray-100.hover\\:shadow-md.transition-shadow .text-3xl.font-bold.text-gray-900');
    if (assignedJobsElement && statistics.jobsAssignedToday !== undefined) {
        assignedJobsElement.textContent = statistics.jobsAssignedToday.toLocaleString();
    }
    
    // Update Completed Jobs card
    const completedJobsElements = document.querySelectorAll('.bg-white.rounded-xl.p-6.shadow-sm.border.border-gray-100.hover\\:shadow-md.transition-shadow .text-3xl.font-bold.text-gray-900');
    if (completedJobsElements.length >= 2 && statistics.jobsCompletedOverall !== undefined) {
        completedJobsElements[1].textContent = statistics.jobsCompletedOverall.toLocaleString();
    }
    
    // Update Pending Jobs card
    if (completedJobsElements.length >= 3 && statistics.pendingJobs !== undefined) {
        completedJobsElements[2].textContent = statistics.pendingJobs.toLocaleString();
    }
}

// Borrowed Items Modal Functions
function openBorrowedItemsModal(bookingId) {
    const modal = document.getElementById('borrowed-items-modal');
    const content = document.getElementById('borrowed-items-content');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="flex justify-center items-center space-x-2 mb-4">
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full loading-dots"></div>
            </div>
            <p class="text-gray-500 text-sm">Loading borrowed equipment...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch borrowed items for this booking
    fetch(`/employee/jobs/${bookingId}/borrowed-items`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                let itemsHtml = `
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Equipment borrowed for this booking:</p>
                    </div>
                    <div class="space-y-3">
                `;
                
                data.items.forEach(item => {
                    const isReturnable = item.is_returnable;
                    const badgeClass = isReturnable ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
                    const badgeText = isReturnable ? 'Returnable' : 'Consumable';
                    const returnStatus = isReturnable ? 'Will be returned when job is completed' : 'Used/depleted item';
                    
                    itemsHtml += `
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <h4 class="font-medium text-gray-900">${item.name}</h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                                        ${badgeText}
                                    </span>
                                </div>
                                <div class="text-lg font-bold text-blue-600">${item.quantity}x</div>
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                <span class="font-medium">${item.category}</span> • Code: ${item.item_code}
                            </div>
                            <div class="text-sm ${isReturnable ? 'text-blue-600' : 'text-orange-600'} font-medium">
                                ${returnStatus}
                            </div>
                        </div>
                    `;
                });
                
                itemsHtml += '</div>';
                content.innerHTML = itemsHtml;
                
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-box-3-line text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Borrowed Equipment</h3>
                        <p class="text-sm text-gray-500">This booking does not have any borrowed equipment.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading borrowed items:', error);
            content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading borrowed equipment.</div>';
        });
}

function closeBorrowedItemsModal() {
    const modal = document.getElementById('borrowed-items-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

{{-- Add CSS for proper modal containment --}}
<style>
    #equipment-modal .modal-content-container {
        position: relative;
        overflow: hidden;
        contain: layout style;
    }
    
    #equipment-modal .equipment-item {
        position: relative;
        z-index: 10;
    }
    
    #equipment-modal .selected-equipment-section {
        position: relative;
        z-index: 10;
        background: white;
    }
    
    #equipment-modal .equipment-content {
        overflow: hidden;
    }
    
    /* Prevent content from overlapping */
    #equipment-modal * {
        position: relative;
        z-index: auto;
    }
    
    #equipment-modal .bg-white {
        background: white !important;
    }
</style>

{{-- Employee Equipment Modal --}}
<div id="equipment-modal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-0 border w-full max-w-4xl shadow-2xl rounded-lg bg-white" style="max-height: calc(100vh - 2rem);">
        <div class="flex flex-col h-full">
            <!-- Header - Fixed at top -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0 bg-white relative z-10">
                <h3 class="text-xl font-semibold text-gray-900">Get Equipment for Job</h3>
                <button onclick="closeEquipmentModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <form id="equipment-form" class="flex flex-col flex-1">
                <input type="hidden" id="equipment-booking-id" name="booking_id">
                
                <!-- Scrollable content area - Takes remaining space -->
                <div id="equipment-content" class="flex-1 overflow-y-auto p-6 bg-white relative z-10 modal-content-container">
                    <!-- Dynamic content will be loaded here -->
                </div>
                
                <!-- Footer - Fixed at bottom -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 p-6 flex-shrink-0 bg-gray-50 relative z-10">
                    <button type="button" onclick="closeEquipmentModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmGetEquipment()" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors cursor-pointer">
                        <i class="ri-tools-line mr-2"></i>
                        Borrow Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Borrowed Items Modal --}}
<div id="borrowed-items-modal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-0 border w-full max-w-4xl shadow-2xl rounded-lg bg-white" style="max-height: calc(100vh - 2rem);">
        <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-semibold text-gray-900">Borrowed Equipment</h3>
                <button onclick="closeBorrowedItemsModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <!-- Scrollable content area -->
            <div id="borrowed-items-content" class="flex-1 overflow-y-auto p-6">
                <!-- Dynamic content will be loaded here -->
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end pt-4 border-t border-gray-200 p-6 flex-shrink-0 bg-gray-50">
                <button onclick="closeBorrowedItemsModal()" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Payment Status Polling Script --}}
<script>
// Payment status polling functionality
let paymentStatusInterval = null;
let lastPaymentStatus = {};

// Function to start payment status polling
function startPaymentStatusPolling() {
    // Only poll if there are jobs in progress with payment proofs
    const hasJobsWithPayments = document.querySelectorAll('button[onclick*="openPaymentModal"]').length > 0;
    if (!hasJobsWithPayments) {
        return; // No need to poll if no jobs with payments
    }
    
    // Check payment status every 3 seconds
    paymentStatusInterval = setInterval(checkPaymentStatus, 3000);
    
    // Also check immediately when page loads
    setTimeout(checkPaymentStatus, 5000); // Check after 5 seconds
}

// Function to stop payment status polling
function stopPaymentStatusPolling() {
    if (paymentStatusInterval) {
        clearInterval(paymentStatusInterval);
        paymentStatusInterval = null;
    }
}

// Function to check payment status
function checkPaymentStatus() {
    fetch('/employee/jobs/payment-status', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.payment_status) {
            // Check if any payment status has changed
            let hasChanges = false;
            
            data.payment_status.forEach(payment => {
                const bookingId = payment.booking_id;
                const currentStatus = `${payment.payment_approved}-${payment.payment_status}`;
                const lastStatus = lastPaymentStatus[bookingId];
                
                console.log(`Booking ${bookingId}: payment_approved=${payment.payment_approved}, payment_status=${payment.payment_status}, currentStatus=${currentStatus}, lastStatus=${lastStatus}`);
                
                if (lastStatus && lastStatus !== currentStatus) {
                    hasChanges = true;
                    console.log(`Payment status changed for booking ${bookingId}: ${lastStatus} → ${currentStatus}`);
                }
                
                lastPaymentStatus[bookingId] = currentStatus;
            });
            
            // If there are changes, refresh the table
            if (hasChanges) {
                console.log('Payment status changes detected, refreshing table...');
                refreshJobTable();
                
                // Show notification
                showPaymentStatusUpdateNotification();
            }
        }
    })
    .catch(error => {
        console.error('Error checking payment status:', error);
        // Don't show error to user, just log it
    });
}

// Function to show payment status update notification
function showPaymentStatusUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'fixed right-4 bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    notification.style.top = '80px';
    notification.style.transform = 'translateX(100%)';
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-notification-line text-xl"></i>
            <div>
                <div class="font-medium">Payment Status Updated</div>
                <div class="text-sm opacity-90">Your job assignments have been refreshed</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Start polling when page loads
document.addEventListener('DOMContentLoaded', function() {
    startPaymentStatusPolling();
});

// Stop polling when page is hidden (to save resources)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPaymentStatusPolling();
    } else {
        startPaymentStatusPolling();
    }
});

// Stop polling when user navigates away
window.addEventListener('beforeunload', function() {
    stopPaymentStatusPolling();
});
</script>
@endpush