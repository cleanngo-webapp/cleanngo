@extends('layouts.employee')

@section('title','Payroll')

@section('content')
<style>
    /* Mobile responsive styles */
    @media (max-width: 640px) {
        /* Make modals mobile-friendly */
        #employee-payroll-receipt-modal {
            width: 95vw !important;
            max-width: 95vw !important;
            margin: 0.5rem !important;
        }
        
        /* Ensure table containers handle overflow */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Make buttons touch-friendly */
        button {
            min-height: 2.5rem;
        }
        
        /* Mobile card adjustments */
        .block.sm\:hidden .p-4 {
            padding: 0.5rem !important;
        }
        
        .block.sm\:hidden .space-y-3 > * + * {
            margin-top: 0.5rem !important;
        }
        
        .block.sm\:hidden .rounded-xl {
            border-radius: 0.5rem !important;
        }
        
        .block.sm\:hidden .flex.gap-2 {
            gap: 0.5rem !important;
        }
        
        .block.sm\:hidden .px-2 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .block.sm\:hidden .py-1\.5 {
            padding-top: 0.375rem !important;
            padding-bottom: 0.375rem !important;
        }
        
        .block.sm\:hidden .text-xs {
            font-size: 0.75rem !important;
            line-height: 1rem !important;
        }
        
        /* Make labels more visible */
        .block.sm\:hidden .text-xs.text-gray-500 {
            font-weight: 600 !important;
            color: #6b7280 !important;
        }
        
        .block.sm\:hidden .grid.gap-4 {
            gap: 0.75rem !important;
        }
        
        .block.sm\:hidden .text-sm {
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }
        
        /* Ensure button text is always visible for payroll cards */
        .block.sm\:hidden button span {
            display: inline !important;
        }
        
        /* Make buttons more prominent */
        .block.sm\:hidden button {
            min-height: 2.5rem !important;
            font-weight: 500 !important;
        }
        
        /* Ultra-minimal spacing for very small screens */
        @media (max-width: 360px) {
            .block.sm\:hidden .flex.gap-2 {
                gap: 0.25rem !important;
            }
            
            .block.sm\:hidden .px-2 {
                padding-left: 0.25rem !important;
                padding-right: 0.25rem !important;
            }
        }
    }
</style>
<div class="max-w-7xl mx-auto px-0 sm:px-0">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-center">Payroll</h1>
    
    {{-- Earnings Summary Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">My Earnings</p>
                    <p class="text-3xl font-bold text-gray-900">₱{{ number_format($monthlyEarnings, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">This month</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jobs Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $monthlyJobsCompleted }}</p>
                    <p class="text-xs text-gray-500 mt-1">This month</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    {{-- Search and Sort Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4 sm:mt-6">
        <div class="p-2 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-payroll" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search payroll by Booking ID" 
                           class="w-full px-3 sm:px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
                <div class="grid grid-cols-2 sm:flex gap-2">
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'completed_at') === 'completed_at' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('completed_at')">
                        <i class="ri-calendar-line mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Sort by Date</span>
                        <span class="sm:hidden">Date</span>
                        <i class="ri-arrow-{{ ($sort ?? 'completed_at') === 'completed_at' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-1 sm:ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'completed_at') === 'total_due_cents' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('total_due_cents')">
                        <i class="ri-money-dollar-circle-line mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Sort by Amount</span>
                        <span class="sm:hidden">Amount</span>
                        <i class="ri-arrow-{{ ($sort ?? 'completed_at') === 'total_due_cents' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-1 sm:ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payroll Records Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-2 sm:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">My Payroll Records</h2>
                    <p class="text-sm text-gray-500 mt-1">View your payment history and earnings</p>
                </div>
            </div>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="block sm:hidden p-2">
            @forelse($payrollRecords as $record)
            <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-sm">
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Date</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            {{ $record->completed_at ? \Carbon\Carbon::parse($record->completed_at)->format('M j, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Booking ID</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $record->booking_code }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Amount</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $record->payroll_amount ? '₱' . number_format($record->payroll_amount, 2) : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Status</p>
                        <div class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($record->payroll_status ?? 'unpaid') === 'paid' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($record->payroll_status ?? 'unpaid') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Payroll Method</p>
                    <p class="text-sm text-gray-900 mt-1">{{ ucfirst($record->payroll_method ?? 'N/A') }}</p>
                </div>
                
                <div class="flex gap-2">
                    @if(($record->payroll_status ?? 'unpaid') === 'paid')
                        <button type="button" onclick="openEmployeeReceipt({{ $record->booking_id }})" class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                            <i class="ri-receipt-line mr-2"></i>
                            <span>View Receipt</span>
                        </button>
                    @else
                        <button type="button" disabled class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            <i class="ri-receipt-line mr-2"></i>
                            <span>View Receipt</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <!-- Empty State Icon -->
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                        <i class="ri-money-dollar-circle-line text-2xl text-gray-400"></i>
                    </div>
                    
                    <!-- Empty State Content -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Payroll Records Found</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            @if(request()->has('search') || request()->has('sort'))
                                No payroll records match your current filters. Try adjusting your search criteria.
                            @else
                                No completed jobs with payments yet. Payroll records will appear here once you complete jobs and payments are processed.
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
            </div>
            @endforelse
        </div>
        
        {{-- Desktop Table View --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Booking ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Payroll Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
                    </tr>
                </thead>
                <tbody id="payroll-table-body" class="bg-white divide-y divide-gray-200">
                    @forelse($payrollRecords as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $record->completed_at ? \Carbon\Carbon::parse($record->completed_at)->format('M j, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $record->booking_code }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $record->payroll_amount ? '₱' . number_format($record->payroll_amount, 2) : 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst($record->payroll_method ?? 'N/A') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($record->payroll_status ?? 'unpaid') === 'paid' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($record->payroll_status ?? 'unpaid') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if(($record->payroll_status ?? 'unpaid') === 'paid')
                                    <button type="button" onclick="openEmployeeReceipt({{ $record->booking_id }})" class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                                        <i class="ri-receipt-line mr-1"></i>
                                        View Receipt
                                    </button>
                                @else
                                    <button type="button" disabled class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <i class="ri-receipt-line mr-1"></i>
                                        View Receipt
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                                    <i class="ri-money-dollar-circle-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Payroll Records Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No payroll records match your current filters. Try adjusting your search criteria.
                                        @else
                                            No completed jobs with payments yet. Payroll records will appear here once you complete jobs and payments are processed.
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
    </div>

</div>

<!-- Payroll Receipt Modal Component -->
@include('components.payroll-receipt-modal', [
    'modalId' => 'employee-payroll-receipt-modal',
    'payrollData' => $payrollData ?? [],
    'bookingId' => null,
    'title' => 'Payroll Details'
])

@endsection

@push('scripts')
<script>
// Search and Sort functionality
let currentSort = '{{ $sort ?? "completed_at" }}';
let currentSortOrder = '{{ $sortOrder ?? "desc" }}';
let searchTimeout;

// Search functionality with AJAX
document.getElementById('search-payroll').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 300);
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

// AJAX search function
function performSearch() {
    const searchTerm = document.getElementById('search-payroll').value;
    const url = new URL('{{ route("employee.payroll") }}', window.location.origin);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    url.searchParams.set('sort', currentSort);
    url.searchParams.set('sortOrder', currentSortOrder);
    
    // Show loading state
    const tableBody = document.getElementById('payroll-table-body');
    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-8 text-center">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <p class="text-gray-500 text-sm">Searching...</p>
            </td>
        </tr>
    `;
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.getElementById('payroll-table-body');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
        });
}

// Clear all filters function
function clearFilters() {
    // Clear search input
    const searchInput = document.getElementById('search-payroll');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset sort
    currentSort = 'completed_at';
    currentSortOrder = 'desc';
    updateSortButtons();
    
    // Perform search to refresh results
    performSearch();
}

// Payroll receipt modal function for employee
function openEmployeeReceipt(bookingId) {
    openPayrollReceipt('employee-payroll-receipt-modal', bookingId, @json($payrollData ?? []));
}
</script>
@endpush


