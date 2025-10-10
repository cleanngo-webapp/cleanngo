@extends('layouts.admin')

@section('title','Payroll')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Payroll</h1>

    {{-- Earnings Summary Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Admin Earnings</p>
                    <p class="text-3xl font-bold text-gray-900">₱{{ number_format($monthlyEarnings, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">This month (after employee payments)</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">₱{{ number_format($monthlyTotalEarnings, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">This month (before deductions)</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jobs Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $monthlyJobsCompleted }}</p>
                    <p class="text-xs text-gray-500 mt-1">This month</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                           id="search-payroll" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search payroll by Booking ID or Employee" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'completed_at') === 'completed_at' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('completed_at')">
                        <i class="ri-calendar-line mr-2"></i>
                        Sort by Date
                        <i class="ri-arrow-{{ ($sort ?? 'completed_at') === 'completed_at' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'completed_at') === 'employee_name' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('employee_name')">
                        <i class="ri-user-line mr-2"></i>
                        Sort by Employee
                        <i class="ri-arrow-{{ ($sort ?? 'completed_at') === 'employee_name' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payroll Records Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Payroll Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Track employee payments and payroll history</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Booking ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Employee</th>
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
                            <div class="text-sm text-gray-900">{{ $record->employee_name }}</div>
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
                                    <button type="button" onclick="openAdminPayrollReceipt({{ $record->booking_id }}, {{ $record->employee_id }})" class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-emerald-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                                        <i class="ri-receipt-line mr-1"></i>
                                        View Receipt
                                    </button>
                                @else
                                    <button type="button" onclick="openUploadPaymentModal({{ $record->booking_id }}, {{ $record->employee_id }}, '{{ $record->employee_name }}')" class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors cursor-pointer">
                                        <i class="ri-upload-line mr-1"></i>
                                        Upload Payment
                                    </button>
                                @endif
                                <button type="button" onclick="openEmployeeQR({{ $record->employee_id }}, '{{ $record->employee_name }}', '{{ $record->gcash_name ?? 'N/A' }}', '{{ $record->gcash_number ?? 'N/A' }}', '{{ $record->qr_code_path ?? '' }}')" class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-purple-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors cursor-pointer">
                                    <i class="ri-qr-code-line mr-1"></i>
                                    View QR
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-money-dollar-circle-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Payroll Records Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No payroll records match your current filters. Try adjusting your search criteria.
                                        @else
                                            No completed jobs with payments yet. Payroll records will appear here once jobs are completed and payments are processed.
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
    'modalId' => 'admin-payroll-receipt-modal',
    'payrollData' => $payrollData ?? [],
    'bookingId' => null,
    'title' => 'Payroll Details'
])

<!-- Employee QR Modal Component -->
<div id="employee-qr-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="qr-modal-title">Employee Payment Information</h3>
                <button type="button" onclick="closeEmployeeQR()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="space-y-4">
                <!-- Employee Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Employee Details</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">GCash Name:</span>
                            <span class="text-sm font-medium text-gray-900" id="qr-gcash-name">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">GCash Number:</span>
                            <span class="text-sm font-medium text-gray-900" id="qr-gcash-number">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Payment QR Code</h4>
                    <div class="flex flex-col items-center space-y-3">
                        <div id="qr-code-container" class="w-48 h-48 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                            <div id="qr-code-placeholder" class="text-center">
                                <i class="ri-qr-code-line text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">QR Code will appear here</p>
                            </div>
                            <img id="qr-code-image" src="" alt="Payment QR Code" class="hidden w-full h-full object-contain rounded">
                        </div>
                        <p class="text-xs text-gray-500 text-center">Scan this QR code to send payment to the employee</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end mt-6">
                <button type="button" onclick="closeEmployeeQR()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Payment Modal Component -->
<div id="upload-payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Upload Payment</h3>
                <button type="button" onclick="closeUploadPaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <form id="upload-payment-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="upload-booking-id" name="booking_id">
                <input type="hidden" id="upload-employee-id" name="employee_id">
                
                <div class="space-y-4">
                    <!-- Employee Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Employee Information</h4>
                        <div class="text-sm text-gray-900" id="upload-employee-name">-</div>
                    </div>
                    
                    <!-- Amount Field -->
                    <div>
                        <label for="payroll-amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                            <input type="number" 
                                   id="payroll-amount" 
                                   name="payroll_amount" 
                                   value="600" 
                                   min="0" 
                                   step="0.01" 
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div>
                        <label for="payroll-method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select id="payroll-method" 
                                name="payroll_method" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <!-- Payment Proof Upload -->
                    <div>
                        <label for="payroll-proof" class="block text-sm font-medium text-gray-700 mb-2">Payment Proof</label>
                        <input type="file" 
                               id="payroll-proof" 
                               name="payroll_proof" 
                               accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Upload an image of the payment proof (JPG, PNG, GIF)</p>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            onclick="closeUploadPaymentModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer">
                        Upload Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    const url = new URL('{{ route("admin.payroll") }}', window.location.origin);
    
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

// Payroll receipt modal function for admin
function openAdminPayrollReceipt(bookingId, employeeId) {
    const compositeKey = bookingId + '_' + employeeId;
    openPayrollReceipt('admin-payroll-receipt-modal', compositeKey, @json($payrollData ?? []));
}

// Employee QR modal functions
function openEmployeeQR(employeeId, employeeName, gcashName, gcashNumber, qrCodePath) {
    // Update modal content with employee information
    document.getElementById('qr-gcash-name').textContent = gcashName || 'N/A';
    document.getElementById('qr-gcash-number').textContent = gcashNumber || 'N/A';
    
    // Handle QR code display
    const qrCodeContainer = document.getElementById('qr-code-container');
    const qrCodePlaceholder = document.getElementById('qr-code-placeholder');
    const qrCodeImage = document.getElementById('qr-code-image');
    
    if (qrCodePath && qrCodePath.trim() !== '') {
        // Show QR code image - ensure proper path formatting
        const fullPath = qrCodePath.startsWith('http') ? qrCodePath : `/storage/${qrCodePath}`;
        qrCodeImage.src = fullPath;
        qrCodeImage.classList.remove('hidden');
        qrCodePlaceholder.classList.add('hidden');
    } else {
        // Show placeholder if no QR code available
        qrCodeImage.classList.add('hidden');
        qrCodePlaceholder.classList.remove('hidden');
        qrCodePlaceholder.innerHTML = `
            <i class="ri-qr-code-line text-4xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-500">No QR code available</p>
        `;
    }
    
    // Show the modal
    document.getElementById('employee-qr-modal').classList.remove('hidden');
}

function closeEmployeeQR() {
    // Hide the modal
    document.getElementById('employee-qr-modal').classList.add('hidden');
    
    // Reset modal content
    document.getElementById('qr-gcash-name').textContent = '-';
    document.getElementById('qr-gcash-number').textContent = '-';
    
    // Reset QR code display
    const qrCodePlaceholder = document.getElementById('qr-code-placeholder');
    const qrCodeImage = document.getElementById('qr-code-image');
    
    qrCodeImage.classList.add('hidden');
    qrCodePlaceholder.classList.remove('hidden');
    qrCodePlaceholder.innerHTML = `
        <i class="ri-qr-code-line text-4xl text-gray-400 mb-2"></i>
        <p class="text-sm text-gray-500">QR Code will appear here</p>
    `;
}

// Close modal when clicking outside of it
document.addEventListener('click', function(event) {
    const modal = document.getElementById('employee-qr-modal');
    const modalContent = modal.querySelector('.relative');
    
    if (event.target === modal) {
        closeEmployeeQR();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEmployeeQR();
        closeUploadPaymentModal();
    }
});

// Upload Payment Modal functions
function openUploadPaymentModal(bookingId, employeeId, employeeName) {
    // Set form data
    document.getElementById('upload-booking-id').value = bookingId;
    document.getElementById('upload-employee-id').value = employeeId;
    document.getElementById('upload-employee-name').textContent = employeeName;
    
    // Reset form
    document.getElementById('payroll-amount').value = '600';
    document.getElementById('payroll-method').value = '';
    document.getElementById('payroll-proof').value = '';
    
    // Show modal
    document.getElementById('upload-payment-modal').classList.remove('hidden');
}

function closeUploadPaymentModal() {
    // Hide modal
    document.getElementById('upload-payment-modal').classList.add('hidden');
    
    // Reset form
    document.getElementById('upload-payment-form').reset();
}

// Handle upload payment form submission
document.getElementById('upload-payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Show loading state
    submitButton.textContent = 'Uploading...';
    submitButton.disabled = true;
    
    // Submit form via AJAX
    fetch('{{ route("admin.payroll.upload-payment") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            closeUploadPaymentModal();
            
            // Refresh the page to show updated data
            window.location.reload();
        } else {
            alert(data.message || 'Error uploading payment. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading payment. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
});

// Close upload modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('upload-payment-modal');
    const modalContent = modal.querySelector('.relative');
    
    if (event.target === modal) {
        closeUploadPaymentModal();
    }
});
</script>
@endpush