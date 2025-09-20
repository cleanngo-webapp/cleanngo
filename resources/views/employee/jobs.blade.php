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
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="jobs-table-body" class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y g:i A') : '—' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $b->customer_name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($b->status === 'in_progress')
                                    @if($b->payment_approved)
                                        <button type="button" onclick="confirmCompleteJob({{ $b->id }}, '{{ $b->code }}')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors cursor-pointer" title="Mark as complete">
                                            <i class="ri-check-line mr-1"></i>
                                            Complete
                                        </button>
                                    @else
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof required" disabled>
                                            <i class="ri-check-line mr-1"></i>
                                            Complete
                                        </button>
                                    @endif
                                    @if($b->payment_status === 'declined' || !$b->payment_proof_id)
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openPaymentModal({{ $b->id }})" title="Attach Payment Proof">
                                            <i class="ri-attachment-line mr-1"></i>
                                            Attach Payment
                                        </button>
                                    @else
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof already uploaded - waiting for admin review" disabled>
                                            <i class="ri-attachment-line mr-1"></i>
                                            Attach Payment
                                        </button>
                                    @endif
                                @elseif($b->status === 'pending' || $b->status === 'confirmed')
                                    @php
                                        $isScheduledToday = \Carbon\Carbon::parse($b->scheduled_start)->isToday();
                                        $canStartJob = $isScheduledToday || $b->status === 'in_progress';
                                    @endphp
                                    @if($canStartJob)
                                        <button type="button" onclick="confirmStartJob({{ $b->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors cursor-pointer" title="Start Job">
                                            <i class="ri-play-line mr-1"></i>
                                            Start Job
                                        </button>
                                    @else
                                        <button type="button" disabled class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" title="Job scheduled for {{ \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y') }}">
                                            <i class="ri-play-line mr-1"></i>
                                            Start Job
                                        </button>
                                    @endif
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpReceipt({{ $b->id }})" title="View Service Summary">
                                    <i class="ri-receipt-line mr-1"></i>
                                    Service Summary
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpLocation({ id: {{ $b->id }}, lat: {{ $b->latitude ?? 0 }}, lng: {{ $b->longitude ?? 0 }} })" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    Location
                                </button>
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
                    <input type="file" name="proof_image" accept="image/*" id="proof-image-input" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500 cursor-pointer" required>
                    <p class="text-xs text-gray-500 mt-1 cursor-pointer">Upload Receipt or Cash in hand image (max 2MB)</p>
                    
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
    
    // Handle file input change
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showImagePreview(file);
            } else {
                hideImagePreview();
            }
        });
    }
    
    // Handle remove preview button
    if (removePreviewBtn) {
        removePreviewBtn.addEventListener('click', function() {
            document.getElementById('proof-image-input').value = '';
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
    
    // Show loading state on the button
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Attaching Payment...';
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
            
            // Refresh the page after a short delay to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Handle validation errors
            showPaymentErrorAlert(data.message || 'An error occurred while uploading the payment proof.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPaymentErrorAlert('An error occurred while uploading the payment proof. Please try again.');
    })
    .finally(() => {
        // Re-enable submit button
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Attach Payment';
        }
    });
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
            // Create and submit the form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/jobs/${jobId}/complete`;
            
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