@extends('layouts.employee')

@section('title','My Jobs')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">My Jobs</h1>

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
                    @foreach($bookings as $b)
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
                                {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($b->status === 'in_progress')
                                    @if($b->payment_approved)
                                        <form method="POST" action="{{ route('employee.jobs.complete', $b->id) }}" class="inline">
                                            @csrf
                                            <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors cursor-pointer" title="Mark as complete">
                                                <i class="ri-check-line mr-1"></i>
                                                Complete
                                            </button>
                                        </form>
                                    @else
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof required" disabled>
                                            <i class="ri-check-line mr-1"></i>
                                            Complete
                                        </button>
                                    @endif
                                    @if($b->payment_status === 'declined' || !$b->payment_proof_id)
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors cursor-pointer" onclick="openPaymentModal({{ $b->id }})" title="Attach Payment Proof">
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
                                    <form method="POST" action="{{ route('employee.jobs.start', $b->id) }}" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors cursor-pointer" title="Start Job">
                                            <i class="ri-play-line mr-1"></i>
                                            Start Job
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpReceipt({{ $b->id }})" title="View Receipt">
                                    <i class="ri-receipt-line mr-1"></i>
                                    Receipt
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openEmpLocation({ id: {{ $b->id }}, lat: {{ $b->latitude ?? 0 }}, lng: {{ $b->longitude ?? 0 }} })" title="View Location">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    Location
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    </div>
    <div id="job-map-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Customer Location</div>
                <button class="cursor-pointer" onclick="const m=document.getElementById('job-map-modal'); m.classList.add('hidden'); m.classList.remove('flex');">✕</button>
            </div>
            <div id="empLocationAddress" class="text-sm mb-1 text-gray-700"></div>
            <div id="empLocationPhone" class="text-xs mb-2 text-gray-500"></div>
            <div id="jobMap" class="h-80 rounded border"></div>
        </div>
    </div>
    <!-- Payment Proof Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Attach Payment Proof</div>
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
                    <select name="payment_method" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof Image</label>
                    <input type="file" name="proof_image" accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500" required>
                    <p class="text-xs text-gray-500 mt-1">Upload receipt or cash in hand image (max 2MB)</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="px-3 py-2 bg-purple-600 text-white rounded cursor-pointer hover:bg-purple-700">Attach Payment</button>
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
}

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
</script>
@endpush