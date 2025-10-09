@extends('layouts.admin')

@section('title','Completed Bookings')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Completed Bookings Page Styles */
.completed-booking-card {
    transition: all 0.3s ease-in-out;
    border-left: 4px solid #10b981;
}

.completed-booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}


/* Loading animation */
.loading-dots {
    animation: loading-dots 1.4s infinite ease-in-out both;
}

.loading-dots:nth-child(1) { animation-delay: -0.32s; }
.loading-dots:nth-child(2) { animation-delay: -0.16s; }

@keyframes loading-dots {
    0%, 80%, 100% {
        transform: scale(0);
    } 40% {
        transform: scale(1);
    }
}
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Completed Bookings</h1>
            <p class="text-sm text-gray-600 mt-1">View all completed bookings and their details</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.bookings') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                <i class="ri-arrow-left-line mr-2"></i>
                Back to Active Bookings
            </a>
        </div>
    </div>

    {{-- Stats Cards Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Completed Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Completed</p>
                    <p id="total-completed-count" class="text-2xl font-bold text-green-600">{{ number_format($totalCompleted ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time completed jobs</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completed This Month Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed This Month</p>
                    <p id="monthly-completed-count" class="text-2xl font-bold text-blue-600">{{ number_format($monthlyCompleted ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ date('F Y') }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Cancelled Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Cancelled</p>
                    <p id="total-cancelled-count" class="text-2xl font-bold text-red-600">{{ number_format($totalCancelled ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time cancelled jobs</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-completed-bookings" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search by booking ID, customer name, or assigned employee" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <select id="status-filter" class="px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">All Status</option>
                        <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'scheduled_start') === 'scheduled_start' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('scheduled_start')">
                        <i class="ri-calendar-line mr-2"></i>
                        Sort by Date
                        <i class="ri-arrow-{{ ($sort ?? 'scheduled_start') === 'scheduled_start' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'scheduled_start') === 'customer_name' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('customer_name')">
                        <i class="ri-user-line mr-2"></i>
                        Sort by Customer
                        <i class="ri-arrow-{{ ($sort ?? 'scheduled_start') === 'customer_name' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Completed Bookings Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900" id="table-title">
                        @if($status === 'cancelled')
                            Cancelled Bookings
                        @else
                            Completed Bookings
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1" id="table-description">
                        @if($status === 'cancelled')
                            View all cancelled bookings and their details
                        @else
                            View all completed bookings and their details
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div id="completed-bookings-table-container" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="completed-bookings-table-body" class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors completed-booking-card" data-booking-id="{{ $b->id }}">
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
                            @if($b->status === 'cancelled')
                                <div class="text-sm text-gray-500 italic">Booking cancelled</div>
                            @elseif(isset($assignedEmployees[$b->id]) && $assignedEmployees[$b->id]->isNotEmpty())
                                <div class="text-sm text-gray-900">
                                    @foreach($assignedEmployees[$b->id] as $employee)
                                        <div class="flex items-center">
                                            <i class="ri-user-line text-gray-400 mr-1"></i>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-500 italic">No employee assigned</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $statusClass = $statusClasses[$b->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $b->status === 'completed' ? 'Completed' : 'Cancelled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($b->status === 'completed')
                                @if($b->payment_proof_id)
                                    @php
                                        $paymentStatusClasses = [
                                            'approved' => 'bg-green-100 text-green-800',
                                            'declined' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $paymentStatusClass = $paymentStatusClasses[$b->payment_status] ?? 'bg-yellow-100 text-yellow-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $paymentStatusClass }}">
                                        {{ ucfirst($b->payment_status ?? 'pending') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 italic">No payment proof</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($b->status === 'completed' && $b->completed_at)
                                    {{ \Carbon\Carbon::parse($b->completed_at)->format('M j, Y g:i A') }}
                                @elseif($b->status === 'cancelled' && $b->updated_at)
                                    {{ \Carbon\Carbon::parse($b->updated_at)->format('M j, Y g:i A') }}
                                @else
                                    —
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-1.5 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-600 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" 
                                        onclick="openBookingInfoModal('completed-booking-info-modal', {{ $b->id }}, 'admin')" 
                                        title="View Booking Information for {{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}">
                                    <i class="ri-information-line mr-1"></i>
                                    Booking Info
                                </button>
                                @if($b->status === 'completed' && $b->payment_proof_id)
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1.5 border border-green-300 shadow-sm text-xs font-medium rounded-md text-green-600 hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors cursor-pointer" 
                                            onclick="openCompletedPaymentProof({{ $b->payment_proof_id }})" 
                                            title="View Payment Proof - Status: {{ ucfirst($b->payment_status ?? 'pending') }}">
                                        <i class="ri-money-dollar-circle-line mr-1"></i>
                                        Payment
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    {{-- Empty State --}}
                    @if($bookings->isEmpty())
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    @if($status === 'cancelled')
                                        <i class="ri-close-circle-line text-2xl text-gray-400"></i>
                                    @else
                                        <i class="ri-checkbox-circle-line text-2xl text-gray-400"></i>
                                    @endif
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        @if($status === 'cancelled')
                                            No Cancelled Bookings Found
                                        @else
                                            No Completed Bookings Found
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('status'))
                                            @if($status === 'cancelled')
                                                No cancelled bookings match your current filters. Try adjusting your search criteria.
                                            @else
                                                No completed bookings match your current filters. Try adjusting your search criteria.
                                            @endif
                                        @else
                                            @if($status === 'cancelled')
                                                No bookings have been cancelled yet.
                                            @else
                                                No bookings have been completed yet.
                                            @endif
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('status'))
                                            <button onclick="clearFilters()" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                                <i class="ri-refresh-line mr-2"></i>
                                                Clear Filters
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.bookings') }}" 
                                           class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                                            <i class="ri-calendar-line mr-2"></i>
                                            View Active Bookings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div id="completed-pagination-container" class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- Booking Info Modal Component -->
    @include('components.booking-info-modal', [
        'modalId' => 'completed-booking-info-modal'
    ])

    <!-- Payment Proof Modal -->
    <div id="completed-payment-proof-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Payment Proof Details</div>
                <button class="cursor-pointer" onclick="closeCompletedPaymentProofModal()">✕</button>
            </div>
            <div id="completed-payment-proof-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>


    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    // Search and Sort functionality for completed bookings
    let currentSort = '{{ $sort ?? "scheduled_start" }}';
    let currentSortOrder = '{{ $sortOrder ?? "desc" }}';
    let searchTimeout;

    // Search functionality with AJAX
    document.getElementById('search-completed-bookings').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performCompletedSearch();
        }, 300);
    });

    // Status filter functionality
    document.getElementById('status-filter').addEventListener('change', function() {
        performCompletedSearch();
    });

    // Sort functionality
    function toggleSort(sortField) {
        if (currentSort === sortField) {
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort = sortField;
            currentSortOrder = 'desc';
        }
        
        // Update button styles and icons
        updateCompletedSortButtons();
        
        // Perform search/sort
        performCompletedSearch();
    }

    function updateCompletedSortButtons() {
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

    // AJAX search function for completed bookings
    function performCompletedSearch() {
        const searchTerm = document.getElementById('search-completed-bookings').value;
        const statusFilter = document.getElementById('status-filter').value;
        const url = new URL('{{ route("admin.completed-bookings") }}', window.location.origin);
        
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        if (statusFilter) {
            url.searchParams.set('status', statusFilter);
        }
        url.searchParams.set('sort', currentSort);
        url.searchParams.set('sortOrder', currentSortOrder);
        
        // Show loading state
        const tableBody = document.getElementById('completed-bookings-table-body');
        const paginationContainer = document.getElementById('completed-pagination-container');
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center">
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                        <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    </div>
                    <p class="text-gray-500 text-sm">Searching...</p>
                </td>
            </tr>
        `;
        paginationContainer.innerHTML = '';
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse the response HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract table body content
                const newTableBody = doc.getElementById('completed-bookings-table-body');
                const newPagination = doc.getElementById('completed-pagination-container');
                const newTableTitle = doc.getElementById('table-title');
                const newTableDescription = doc.getElementById('table-description');
                
                if (newTableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                }
                if (newPagination) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
                if (newTableTitle) {
                    document.getElementById('table-title').innerHTML = newTableTitle.innerHTML;
                }
                if (newTableDescription) {
                    document.getElementById('table-description').innerHTML = newTableDescription.innerHTML;
                }
                
                // Update URL without page refresh
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Search error:', error);
                tableBody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
            });
    }

    // Clear all filters function
    function clearFilters() {
        // Clear search input
        const searchInput = document.getElementById('search-completed-bookings');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Clear status filter
        const statusSelect = document.getElementById('status-filter');
        if (statusSelect) {
            statusSelect.value = '';
        }
        
        // Reset sort
        currentSort = 'scheduled_start';
        currentSortOrder = 'desc';
        updateCompletedSortButtons();
        
        // Perform search to refresh results
        performCompletedSearch();
    }


    // Payment proof modal handlers for completed bookings
    function openCompletedPaymentProof(proofId) {
        const modal = document.getElementById('completed-payment-proof-modal');
        const content = document.getElementById('completed-payment-proof-content');
        
        // Show loading state
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <p class="text-gray-500 text-sm">Loading payment proof details...</p>
            </div>
        `;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Fetch payment proof details
        fetch(`/admin/payment-proof/${proofId}/details`)
            .then(response => response.json())
            .then(data => {
                content.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Payment Information</h3>
                            <div class="space-y-2 text-sm">
                                <div><span class="font-medium">Amount:</span> PHP ${parseFloat(data.amount).toLocaleString()}</div>
                                <div><span class="font-medium">Method:</span> ${data.payment_method.toUpperCase()}</div>
                                <div><span class="font-medium">Status:</span> 
                                    <span class="px-2 py-1 text-xs rounded-full
                                        ${data.status === 'approved' ? 'bg-green-100 text-green-800' : 
                                          data.status === 'declined' ? 'bg-red-100 text-red-800' : 
                                          'bg-yellow-100 text-yellow-800'}">
                                        ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                    </span>
                                </div>
                                <div><span class="font-medium">Uploaded:</span> ${data.created_at}</div>
                                ${data.reviewed_by ? `<div><span class="font-medium">Reviewed by:</span> ${data.reviewed_by}</div>` : ''}
                                ${data.reviewed_at ? `<div><span class="font-medium">Reviewed at:</span> ${data.reviewed_at}</div>` : ''}
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Payment Proof Image</h3>
                            <img src="${data.image_url}" alt="Payment Proof" class="w-full h-64 object-cover rounded-lg">
                        </div>
                    </div>
                    ${data.admin_notes ? `
                        <div class="mt-4">
                            <h3 class="font-medium text-gray-900 mb-2">Admin Notes</h3>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">${data.admin_notes}</p>
                        </div>
                    ` : ''}
                `;
            })
            .catch(error => {
                console.error('Error loading payment proof:', error);
                content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading payment proof details.</div>';
            });
    }
    
    function closeCompletedPaymentProofModal() {
        const modal = document.getElementById('completed-payment-proof-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    </script>
    @endpush
</div>
@endsection
