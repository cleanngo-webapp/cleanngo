@extends('layouts.admin')

@section('title','Bookings')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-center">Manage Bookings</h1>

    {{-- Stats Cards Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Total Bookings Card --}}
                <div class="bg-white rounded-xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBookings ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">All time bookings</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Today's Bookings Card --}}
                <div class="bg-white rounded-xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($todayBookings ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Scheduled for today</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active Services Card --}}
                <div class="bg-white rounded-xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Services</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($activeServices ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Currently in progress</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Completed Jobs Today Card --}}
                <div class="bg-white rounded-xl p-7 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed Today</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($completedJobsToday ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Jobs finished today</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                           id="search-bookings" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search bookings by ID, Customer Name, or Employee" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex gap-2">
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
                    <button type="button" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'scheduled_start') === 'status' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('status')">
                        <i class="ri-flag-line mr-2"></i>
                        Sort by Status
                        <i class="ri-arrow-{{ ($sort ?? 'scheduled_start') === 'status' && ($sortOrder ?? 'desc') === 'asc' ? 'up' : 'down' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Records Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Booking Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage all customer bookings and assignments</p>
                </div>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors cursor-pointer" onclick="document.getElementById('create-booking-modal').classList.remove('hidden')">
                    <i class="ri-add-line"></i>
                    Add Booking
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proof of Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookings-table-body" class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $b)
                    <tr class="hover:bg-gray-50 transition-colors" data-booking-id="{{ $b->id }}">
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
                            @if($b->status === 'pending')
                                <div class="text-sm text-gray-500 italic">Confirm booking first</div>
                            @elseif($b->status === 'cancelled')
                                <div class="text-sm text-gray-500 italic">Booking cancelled</div>
                            @elseif(!empty($b->assigned_employee_id))
                                <div class="text-sm text-gray-900">{{ $b->employee_name ?? '—' }}</div>
                            @else
                                <form method="post" action="{{ url('/admin/bookings/'.$b->id.'/assign') }}" class="assign-form inline" data-booking-id="{{ $b->id }}" data-booking-code="{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}">
                                    @csrf
                                    <select id="assign-select-{{ $b->id }}" name="employee_user_id" class="text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500 assign-select cursor-pointer">
                                        <option class="cursor-pointer" value="">Assign Employee</option>
                                        @foreach($employees as $e)
                                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($b->status === 'pending')
                                {{-- Show confirmation buttons for pending bookings --}}
                                <div class="flex gap-2">
                                    <button id="confirm-btn-{{ $b->id }}" class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors cursor-pointer" 
                                            onclick="openConfirmModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}', 'confirm')">
                                        Confirm
                                    </button>
                                    <button id="cancel-btn-{{ $b->id }}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors cursor-pointer" 
                                            onclick="openConfirmModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}', 'cancel')">
                                        Cancel
                                    </button>
                                </div>
                            @else
                                {{-- Show status with colored span --}}
                                @php
                                    $statusClasses = [
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusClass = $statusClasses[$b->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ $b->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $b->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($b->status === 'in_progress' || $b->status === 'completed')
                                @if($b->payment_proof_id)
                                    <button class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                        @php
                                            $paymentStatusClasses = [
                                                'approved' => 'bg-green-100 text-green-800',
                                                'declined' => 'bg-red-100 text-red-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800'
                                            ];
                                            $paymentStatusClass = $paymentStatusClasses[$b->payment_status] ?? 'bg-yellow-100 text-yellow-800';
                                        @endphp
                                        {{ $paymentStatusClass }}
                                        hover:opacity-80 transition-colors cursor-pointer"
                                        onclick="openPaymentProofModal({{ $b->payment_proof_id }})" 
                                        title="View payment proof">
                                        <i class="ri-receipt-line mr-1"></i>
                                        @if($b->payment_status === 'approved')
                                            Approved
                                        @elseif($b->payment_status === 'declined')
                                            Declined
                                        @else
                                            View Payment
                                        @endif
                                    </button>
                                @else
                                    <span class="text-xs text-gray-500 italic">No proof uploaded</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($b->status === 'confirmed')
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-600 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openStatusChangeModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}')" title="Change Status">
                                        <i class="ri-arrow-up-down-line"></i>
                                    </button>
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-600 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openAdminReceipt({{ $b->id }})" title="View Service Summary">
                                    <i class="ri-receipt-line"></i>
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-600 hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openLocation({{ $b->id }})" title="View Location">
                                    <i class="ri-map-pin-line"></i>                                    
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    {{-- Empty State --}}
                    @if($bookings->isEmpty())
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-calendar-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Bookings Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('status') || request()->has('date_from') || request()->has('date_to'))
                                            No bookings match your current filters. Try adjusting your search criteria.
                                        @else
                                            Get started by creating your first booking or wait for customers to make bookings.
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('status') || request()->has('date_from') || request()->has('date_to'))
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
                    @endif
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-100">
            {{ $bookings->links() }}
        </div>
    </div>

    <div id="create-booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Create Booking</div>
                <button onclick="document.getElementById('create-booking-modal').classList.add('hidden')">✕</button>
            </div>
            <form method="POST" action="{{ url('/admin/bookings') }}" class="grid grid-cols-2 gap-3">
                @csrf
                <label class="text-sm col-span-2">Customer
                    <select name="user_id" class="border rounded px-2 py-1 w-full" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm">Date
                    <input type="date" name="date" class="border rounded px-2 py-1 w-full" required>
                </label>
                <label class="text-sm">Time
                    <input type="time" name="time" class="border rounded px-2 py-1 w-full" required>
                </label>
                <label class="text-sm col-span-2">Assign Employee (optional)
                    <select name="employee_user_id" class="border rounded px-2 py-1 w-full">
                        <option value="">—</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm col-span-2">Notes
                    <textarea name="summary" class="border rounded px-2 py-1 w-full" placeholder="Service details"></textarea>
                </label>
                <div class="col-span-2 flex justify-end gap-2">
                    <button class="px-3 py-2 bg-emerald-700 text-white rounded">Save</button>
                </div>
            </form>
        </div>
        </div>

    <!-- Assign Confirmation Modal -->
    <div id="assign-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Confirm Assignment</div>
                <button class="cursor-pointer" id="assignModalClose">✕</button>
            </div>
            <p id="assignModalText" class="mb-4 text-sm">Are you sure you want to assign this employee? This cannot be changed later.</p>
            <div class="flex justify-end gap-2">
                <button id="assignModalCancel" class="px-3 py-2 bg-gray-500 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Cancel</button>
                <button id="assignModalConfirm" class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Confirm</button>
            </div>
        </div>
        </div>

    <!-- Receipt Modal Component -->
    @include('components.receipt-modal', [
        'modalId' => 'receipt-modal',
        'receiptData' => $receiptData ?? [],
        'bookingId' => null
    ])

    <!-- Status Change Modal -->
    <div id="status-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Confirm Status Change</div>
                <button class="cursor-pointer" onclick="closeStatusModal()">✕</button>
            </div>
            <p id="statusModalText" class="mb-4 text-sm">Are you sure?</p>
            <form id="statusForm" method="post" class="hidden"></form>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeStatusModal()">Cancel</button>
                <button id="statusModalConfirm" class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Location Modal -->
    <div id="location-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-xl p-4 m-4">
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-lg">Customer Location</div>
                <button class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl font-bold" onclick="closeLocation()">✕</button>
            </div>
            <div id="locationAddress" class="text-sm mb-3 text-gray-700 bg-gray-50 p-2 rounded border"></div>
            <div id="locationPhone" class="text-xs mb-3 text-gray-500"></div>
            <div id="adminLocationMap" class="h-80 rounded border border-gray-300 bg-gray-100"></div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" onclick="closeLocation()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Booking Confirmation Modal -->
    <div id="booking-confirm-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold" id="confirmModalTitle">Confirm Booking</div>
                <button class="cursor-pointer" onclick="closeConfirmModal()">✕</button>
            </div>
            <p id="confirmModalText" class="mb-4 text-sm">Are you sure you want to confirm this booking?</p>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeConfirmModal()">Cancel</button>
                <button id="confirmModalAction" class="px-3 py-2 text-white rounded cursor-pointer hover:opacity-90">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div id="status-change-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Change Booking Status</div>
                <button class="cursor-pointer" onclick="closeStatusChangeModal()">✕</button>
            </div>
            <p id="statusChangeModalText" class="mb-4 text-sm">Select new status for this booking:</p>
            <div class="mb-4">
                <select id="statusChangeSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
                    <!-- Options will be populated dynamically based on booking date -->
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeStatusChangeModal()">Cancel</button>
                <button id="statusChangeConfirm" class="px-3 py-2 bg-emerald-600 text-white rounded cursor-pointer hover:bg-emerald-700">Update Status</button>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="payment-proof-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-2xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Proof of Payment Details</div>
                <button class="cursor-pointer" onclick="closePaymentProofModal()">✕</button>
            </div>
            <div id="payment-proof-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Decline Payment Proof Modal -->
    <div id="decline-payment-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Decline Payment Proof</div>
                <button class="cursor-pointer" onclick="closeDeclineModal()">✕</button>
            </div>
            <p class="mb-4 text-sm text-gray-600">Please provide a reason for declining this payment proof:</p>
            <form id="decline-form" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea name="admin_notes" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-red-500 focus:ring-red-500" placeholder="Enter reason for declining..." required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeDeclineModal()">Cancel</button>
                    <button type="button" class="px-3 py-2 bg-red-600 text-white rounded cursor-pointer hover:bg-red-700" onclick="confirmDecline()">Decline Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve Payment Confirmation Modal -->
    <div id="approve-confirmation-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Confirm Payment Approval</div>
                <button class="cursor-pointer" onclick="closeApproveConfirmationModal()">✕</button>
            </div>
            <p class="mb-4 text-sm text-gray-600">Are you sure you want to approve this payment proof? This action will allow the employee to complete the job.</p>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeApproveConfirmationModal()">Cancel</button>
                <button id="confirmApproveBtn" class="px-3 py-2 bg-green-600 text-white rounded cursor-pointer hover:bg-green-700">Yes, Approve Payment</button>
            </div>
        </div>
    </div>

    <!-- Decline Payment Confirmation Modal -->
    <div id="decline-confirmation-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Confirm Payment Decline</div>
                <button class="cursor-pointer" onclick="closeDeclineConfirmationModal()">✕</button>
            </div>
            <p class="mb-4 text-sm text-gray-600">Are you sure you want to decline this payment proof? The employee will be able to upload a new payment proof.</p>
            <div class="mb-4">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm font-medium text-gray-700 mb-1">Decline Reason:</p>
                    <p id="decline-reason-preview" class="text-sm text-gray-600"></p>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeDeclineConfirmationModal()">Cancel</button>
                <button id="confirmDeclineBtn" class="px-3 py-2 bg-red-600 text-white rounded cursor-pointer hover:bg-red-700">Yes, Decline Payment</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    // Search and Sort functionality
    let currentSort = '{{ $sort ?? "scheduled_start" }}';
    let currentSortOrder = '{{ $sortOrder ?? "desc" }}';
    let searchTimeout;

    // Search functionality with AJAX
    document.getElementById('search-bookings').addEventListener('input', function() {
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
        const searchTerm = document.getElementById('search-bookings').value;
        const url = new URL('{{ route("admin.bookings") }}', window.location.origin);
        
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        url.searchParams.set('sort', currentSort);
        url.searchParams.set('sortOrder', currentSortOrder);
        
        // Show loading state
        const tableBody = document.getElementById('bookings-table-body');
        const paginationContainer = document.getElementById('pagination-container');
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center">
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
                const newTableBody = doc.getElementById('bookings-table-body');
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
                tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
            });
    }

    // Clear all filters function
    function clearFilters() {
        // Clear search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Clear status filter
        const statusSelect = document.getElementById('status-filter');
        if (statusSelect) {
            statusSelect.value = '';
        }
        
        // Clear date filters
        const dateFromInput = document.getElementById('date-from');
        const dateToInput = document.getElementById('date-to');
        if (dateFromInput) {
            dateFromInput.value = '';
        }
        if (dateToInput) {
            dateToInput.value = '';
        }
        
        // Reset sort
        currentSort = 'scheduled_start';
        currentSortOrder = 'desc';
        updateSortButtons();
        
        // Perform search to refresh results
        performSearch();
    }
    </script>
    <script>
    const receiptData = @json($receiptData ?? []);
    const locationsData = @json($locationsData ?? []);
    function peso(v){
        return 'PHP ' + Number(v||0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    // Receipt functions now handled by the component
    function openAdminReceipt(bookingId){
        openReceipt('receipt-modal', bookingId, receiptData);
    }
    // Location modal handlers and Leaflet map
    let adminMap = null; let adminMarker = null;
    function openLocation(bookingId){
        const data = locationsData[String(bookingId)] || locationsData[bookingId];
        const modal = document.getElementById('location-modal');
        const addr = document.getElementById('locationAddress');
        const phone = document.getElementById('locationPhone');
        addr.textContent = data?.address || 'No address available';
        phone.textContent = data?.phone ? ('Contact: ' + data.phone) : '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            if (!adminMap) {
                adminMap = L.map('adminLocationMap').setView([13.0, 122.0], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(adminMap);
            }
            const lat = data?.lat ?? 0; const lng = data?.lng ?? 0;
            adminMap.setView([lat, lng], (lat && lng) ? 15 : 5);
            if (!adminMarker) adminMarker = L.marker([lat, lng]).addTo(adminMap);
            adminMarker.setLatLng([lat, lng]);
            // Fix tiles not rendering when modal opens
            setTimeout(() => { if (adminMap) adminMap.invalidateSize(true); }, 100);
        }, 50);
    }
    function closeLocation(){
        const modal = document.getElementById('location-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Booking confirmation modal handlers
    let pendingConfirmAction = null;
    function openConfirmModal(bookingId, bookingCode, action) {
        pendingConfirmAction = { bookingId, bookingCode, action };
        const modal = document.getElementById('booking-confirm-modal');
        const title = document.getElementById('confirmModalTitle');
        const text = document.getElementById('confirmModalText');
        const actionBtn = document.getElementById('confirmModalAction');
        
        if (action === 'confirm') {
            title.textContent = 'Confirm Booking';
            text.textContent = `Are you sure you want to confirm booking ${bookingCode}? This will allow employee assignment and status changes.`;
            actionBtn.textContent = 'Confirm Booking';
            actionBtn.className = 'px-3 py-2 bg-emerald-600 text-white rounded cursor-pointer hover:bg-emerald-700';
        } else if (action === 'cancel') {
            title.textContent = 'Cancel Booking';
            text.textContent = `Are you sure you want to cancel booking ${bookingCode}? This action cannot be undone.`;
            actionBtn.textContent = 'Cancel Booking';
            actionBtn.className = 'px-3 py-2 bg-red-600 text-white rounded cursor-pointer hover:bg-red-700';
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeConfirmModal() {
        const modal = document.getElementById('booking-confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingConfirmAction = null;
    }
    
    // Handle confirmation modal action
    document.getElementById('confirmModalAction').addEventListener('click', function() {
        if (!pendingConfirmAction) return;
        
        const { bookingId, action } = pendingConfirmAction;
        
        // Show loading state on the table buttons
        const confirmBtn = document.getElementById(`confirm-btn-${bookingId}`);
        const cancelBtn = document.getElementById(`cancel-btn-${bookingId}`);
        
        if (action === 'confirm' && confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
        } else if (action === 'cancel' && cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
        }
        
        // Submit via AJAX
        submitBookingActionViaAjax(bookingId, action, { confirmBtn, cancelBtn });
        
        closeConfirmModal();
    });
    
    // Status change modal handlers
    let pendingStatusChange = null;
    function openStatusChangeModal(bookingId, bookingCode) {
        pendingStatusChange = { bookingId, bookingCode };
        const modal = document.getElementById('status-change-modal');
        const text = document.getElementById('statusChangeModalText');
        const select = document.getElementById('statusChangeSelect');
        
        // Get booking date from the table row
        const bookingRow = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
        let bookingDate = null;
        if (bookingRow) {
            const dateCell = bookingRow.querySelector('td:nth-child(2) .text-sm');
            if (dateCell) {
                const dateText = dateCell.textContent.trim();
                if (dateText !== '—') {
                    bookingDate = new Date(dateText);
                }
            }
        }
        
        // Determine available status options based on booking date
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const isBookingToday = bookingDate && bookingDate.toDateString() === today.toDateString();
        
        // Clear existing options
        select.innerHTML = '';
        
        if (isBookingToday) {
            // If booking is today, show all status options
            text.textContent = `Select new status for booking ${bookingCode} (scheduled today):`;
            select.innerHTML = `
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            `;
        } else {
            // If booking is in the future, only show cancelled option
            text.textContent = `Select new status for booking ${bookingCode} (scheduled for future):`;
            select.innerHTML = `
                <option value="cancelled">Cancelled</option>
            `;
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeStatusChangeModal() {
        const modal = document.getElementById('status-change-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingStatusChange = null;
    }
    
    // Handle status change confirmation with SweetAlert
    document.getElementById('statusChangeConfirm').addEventListener('click', function() {
        if (!pendingStatusChange) return;
        
        const newStatus = document.getElementById('statusChangeSelect').value;
        const statusText = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Confirm Status Change?',
            text: `Are you sure you want to change the status to "${statusText}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Change Status',
            cancelButtonText: 'Cancel',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/bookings/${pendingStatusChange.bookingId}/status`;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                
                const status = document.createElement('input');
                status.type = 'hidden';
                status.name = 'status';
                status.value = newStatus;
                
                // If status is completed, add bypass payment proof flag
                if (newStatus === 'completed') {
                    const bypassPayment = document.createElement('input');
                    bypassPayment.type = 'hidden';
                    bypassPayment.name = 'bypass_payment_proof';
                    bypassPayment.value = '1';
                    form.appendChild(bypassPayment);
                }
                
                form.appendChild(csrf);
                form.appendChild(status);
                document.body.appendChild(form);
                form.submit();
                
                closeStatusChangeModal();
            }
        });
    });
    
    // Payment proof modal handlers
    let currentPaymentProofId = null;
    function openPaymentProofModal(proofId) {
        currentPaymentProofId = proofId;
        const modal = document.getElementById('payment-proof-modal');
        const content = document.getElementById('payment-proof-content');
        
        // Show loading state with preloader
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
                                <div><span class="font-medium">Employee:</span> ${data.employee_name}</div>
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
                    ${data.status === 'pending' ? `
                        <div class="mt-4 flex gap-2">
                            <button onclick="approvePaymentProof(${proofId})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 cursor-pointer">
                                <i class="ri-check-line mr-1"></i> Approve
                            </button>
                            <button onclick="declinePaymentProof(${proofId})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
                                <i class="ri-close-line mr-1"></i> Decline
                            </button>
                        </div>
                    ` : ''}
                `;
            })
            .catch(error => {
                console.error('Error loading payment proof:', error);
                content.innerHTML = '<div class="text-center py-4 text-red-500">Error loading payment proof details.</div>';
            });
    }
    
    function closePaymentProofModal() {
        const modal = document.getElementById('payment-proof-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentPaymentProofId = null;
    }
    
    let currentProofId = null;
    
    function approvePaymentProof(proofId) {
        currentProofId = proofId;
        // Close the payment proof modal
        closePaymentProofModal();
        
        // Open the approve confirmation modal
        const approveModal = document.getElementById('approve-confirmation-modal');
        approveModal.classList.remove('hidden');
        approveModal.classList.add('flex');
    }
    
    function declinePaymentProof(proofId) {
        currentProofId = proofId;
        // Close the payment proof modal
        closePaymentProofModal();
        
        // Open the decline modal
        const declineModal = document.getElementById('decline-payment-modal');
        const declineForm = document.getElementById('decline-form');
        
        // Set the form action
        declineForm.action = `/admin/payment-proof/${proofId}/decline`;
        
        // Clear any previous notes
        declineForm.querySelector('textarea[name="admin_notes"]').value = '';
        
        declineModal.classList.remove('hidden');
        declineModal.classList.add('flex');
    }
    
    function closeDeclineModal() {
        const declineModal = document.getElementById('decline-payment-modal');
        declineModal.classList.add('hidden');
        declineModal.classList.remove('flex');
    }
    
    function confirmDecline() {
        const declineReason = document.querySelector('#decline-form textarea[name="admin_notes"]').value.trim();
        
        if (!declineReason) {
            // Show inline error message instead of browser alert
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mb-4 p-3 bg-red-50 border border-red-200 rounded-lg';
            errorDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="ri-error-warning-line text-red-500"></i>
                    <span class="text-sm text-red-700">Please provide a reason for declining the payment proof.</span>
                </div>
            `;
            
            // Remove any existing error messages
            const existingError = document.querySelector('#decline-form .bg-red-50');
            if (existingError) {
                existingError.remove();
            }
            
            // Insert error message at the top of the form
            const form = document.getElementById('decline-form');
            form.insertBefore(errorDiv, form.firstChild);
            
            // Auto-remove error message after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
            
            return;
        }
        
        // Close the decline modal
        closeDeclineModal();
        
        // Open the decline confirmation modal
        const declineConfirmationModal = document.getElementById('decline-confirmation-modal');
        const reasonPreview = document.getElementById('decline-reason-preview');
        
        // Show the decline reason in the confirmation modal
        reasonPreview.textContent = declineReason;
        
        declineConfirmationModal.classList.remove('hidden');
        declineConfirmationModal.classList.add('flex');
    }
    
    function closeApproveConfirmationModal() {
        const approveModal = document.getElementById('approve-confirmation-modal');
        approveModal.classList.add('hidden');
        approveModal.classList.remove('flex');
        currentProofId = null;
    }
    
    function closeDeclineConfirmationModal() {
        const declineModal = document.getElementById('decline-confirmation-modal');
        declineModal.classList.add('hidden');
        declineModal.classList.remove('flex');
        currentProofId = null;
    }
    
    // Handle final approval
    document.getElementById('confirmApproveBtn').addEventListener('click', function() {
        if (!currentProofId) return;
        
        // Show loading state on the button
        const button = this;
        const originalText = button.textContent;
        button.disabled = true;
        button.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Approving...';
        
        // Submit via AJAX
        submitPaymentApprovalViaAjax(currentProofId, button, originalText);
    });
    
    // Handle final decline
    document.getElementById('confirmDeclineBtn').addEventListener('click', function() {
        if (!currentProofId) return;
        
        // Show loading state on the button
        const button = this;
        const originalText = button.textContent;
        button.disabled = true;
        button.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Declining...';
        
        // Get decline reason
        const declineReason = document.querySelector('#decline-form textarea[name="admin_notes"]').value.trim();
        
        // Submit via AJAX
        submitPaymentDeclineViaAjax(currentProofId, declineReason, button, originalText);
    });
    
    // Status modal handlers
    let pendingStatus = null;
    function openStatusModal(bookingId, bookingCode, newStatus){
        pendingStatus = { bookingId, bookingCode, newStatus };
        const modal = document.getElementById('status-modal');
        const txt = document.getElementById('statusModalText');
        txt.textContent = 'Change status of '+bookingCode+' to '+newStatus.replace('_',' ').toUpperCase()+'?';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeStatusModal(){
        const modal = document.getElementById('status-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        // reset selects if cancelled
        if (pendingStatus){
            const sel = document.querySelector(`.status-select[data-booking-id="${pendingStatus.bookingId}"]`);
            if (sel) sel.value = sel.getAttribute('data-current') || 'pending';
        }
        pendingStatus = null;
    }
    document.getElementById('statusModalConfirm').addEventListener('click', function(){
        if (!pendingStatus) return;
        // Build and submit a form dynamically
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/bookings/${pendingStatus.bookingId}/status`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const status = document.createElement('input');
        status.type = 'hidden'; status.name = 'status'; status.value = pendingStatus.newStatus;
        form.appendChild(csrf); form.appendChild(status);
        document.body.appendChild(form);
        form.submit();
        closeStatusModal();
    });
    (function(){
        let pendingAssignForm = null;
        let pendingAssignSelect = null;
        const modal = document.getElementById('assign-modal');
        const txt = document.getElementById('assignModalText');
        function openModal(form, employeeName){
            pendingAssignForm = form;
            pendingAssignSelect = form.querySelector('select[name="employee_user_id"]');
            const code = form.getAttribute('data-booking-code');
            txt.textContent = 'Assign '+(employeeName||'this employee')+' to '+code+'? This cannot be changed later.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // reset selection if cancelled
            if (pendingAssignSelect) { pendingAssignSelect.value = ''; }
        }
        document.getElementById('assignModalClose').addEventListener('click', closeModal);
        document.getElementById('assignModalCancel').addEventListener('click', closeModal);
        document.getElementById('assignModalConfirm').addEventListener('click', function(){
            if (pendingAssignForm) {
                const bookingId = pendingAssignForm.getAttribute('data-booking-id');
                const selectId = `assign-select-${bookingId}`;
                const select = document.getElementById(selectId);
                const confirmBtn = document.getElementById('assignModalConfirm');
                
                // Show loading state on the confirm button
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
                
                // Submit via AJAX
                submitEmployeeAssignmentViaAjax(pendingAssignForm, select, confirmBtn);
            }
        });
        // Intercept change of assign selects (moved to Assigned Employee column)
        document.querySelectorAll('.assign-form .assign-select').forEach(function(sel){
            sel.addEventListener('change', function(){
                if (!sel.value) return;
                const form = sel.closest('.assign-form');
                const name = sel.options[sel.selectedIndex].textContent.trim();
                openModal(form, name);
            });
        });
        // Status selectors are now handled by the status change button in actions column
    })();
    
    // AJAX submission functions for booking actions
    function submitBookingActionViaAjax(bookingId, action, buttons = null) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        formData.append('action', action);
        
        fetch(`/admin/bookings/${bookingId}/confirm`, {
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
                showAdminSuccessAlert(data.message, data.booking_code);
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Handle errors
                showAdminErrorAlert(data.message || 'An error occurred.');
                
                // Reset buttons if provided
                if (buttons) {
                    if (buttons.confirmBtn) {
                        buttons.confirmBtn.disabled = false;
                        buttons.confirmBtn.innerHTML = 'Confirm';
                    }
                    if (buttons.cancelBtn) {
                        buttons.cancelBtn.disabled = false;
                        buttons.cancelBtn.innerHTML = 'Cancel';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAdminErrorAlert('An error occurred. Please try again.');
            
            // Reset buttons if provided
            if (buttons) {
                if (buttons.confirmBtn) {
                    buttons.confirmBtn.disabled = false;
                    buttons.confirmBtn.innerHTML = 'Confirm';
                }
                if (buttons.cancelBtn) {
                    buttons.cancelBtn.disabled = false;
                    buttons.cancelBtn.innerHTML = 'Cancel';
                }
            }
        });
    }
    
    function submitEmployeeAssignmentViaAjax(form, select = null, confirmBtn = null) {
        const formData = new FormData(form);
        
        // Debug: Log form data to console
        console.log('Form action:', form.action);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        // Show loading state on the select dropdown
        if (select) {
            select.disabled = true;
            select.innerHTML = '<option>Assigning...</option>';
        }
        
        fetch(form.action, {
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
                showAdminSuccessAlert(data.message, data.booking_code, data.employee_name);
                
                // Close the modal
                const modal = document.getElementById('assign-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Handle errors
                showAdminErrorAlert(data.message || 'An error occurred.');
                
                // Reset confirm button
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = 'Confirm';
                }
                
                // Reset select if provided
                if (select) {
                    select.disabled = false;
                    select.innerHTML = `
                        <option value="">Assign Employee</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAdminErrorAlert('An error occurred. Please try again.');
            
            // Reset confirm button
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Confirm';
            }
            
            // Reset select if provided
            if (select) {
                select.disabled = false;
                select.innerHTML = `
                    <option value="">Assign Employee</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                    @endforeach
                `;
            }
        });
    }
    
    // Show admin success alert that auto-disappears
    function showAdminSuccessAlert(message, bookingCode, employeeName = null) {
        const alert = document.createElement('div');
        alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
        alert.style.top = '80px'; // Position below the navigation bar
        alert.style.transform = 'translateX(100%)';
        
        let alertContent = `
            <div class="flex items-center space-x-3">
                <i class="ri-check-line text-xl"></i>
                <div>
                    <div class="font-medium">${message}</div>
                    <div class="text-sm opacity-90">Booking: ${bookingCode}</div>
        `;
        
        if (employeeName) {
            alertContent += `<div class="text-sm opacity-90">Employee: ${employeeName}</div>`;
        }
        
        alertContent += `
                </div>
            </div>
        `;
        
        alert.innerHTML = alertContent;
        
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
    
    // Show admin error alert
    function showAdminErrorAlert(message) {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'OK'
        });
    }
    
    // AJAX submission functions for payment proof actions
    function submitPaymentApprovalViaAjax(proofId, button, originalText) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        
        fetch(`/admin/payment-proof/${proofId}/approve`, {
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
                showPaymentActionSuccessAlert(data.message, 'approved');
                
                // Close the approval modal
                closeApproveConfirmationModal();
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Handle errors
                showAdminErrorAlert(data.message || 'An error occurred while approving the payment proof.');
                
                // Reset button
                button.disabled = false;
                button.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAdminErrorAlert('An error occurred while approving the payment proof. Please try again.');
            
            // Reset button
            button.disabled = false;
            button.textContent = originalText;
        });
    }
    
    function submitPaymentDeclineViaAjax(proofId, declineReason, button, originalText) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        formData.append('admin_notes', declineReason);
        
        fetch(`/admin/payment-proof/${proofId}/decline`, {
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
                showPaymentActionSuccessAlert(data.message, 'declined');
                
                // Close the decline modal
                closeDeclineConfirmationModal();
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Handle errors
                showAdminErrorAlert(data.message || 'An error occurred while declining the payment proof.');
                
                // Reset button
                button.disabled = false;
                button.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAdminErrorAlert('An error occurred while declining the payment proof. Please try again.');
            
            // Reset button
            button.disabled = false;
            button.textContent = originalText;
        });
    }
    
    // Show payment action success alert that auto-disappears
    function showPaymentActionSuccessAlert(message, action) {
        const alert = document.createElement('div');
        alert.className = 'fixed right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
        alert.style.top = '80px'; // Position below the navigation bar
        alert.style.transform = 'translateX(100%)';
        
        const iconClass = action === 'approved' ? 'ri-check-line' : 'ri-close-line';
        const actionText = action === 'approved' ? 'Payment Approved' : 'Payment Declined';
        
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
    </script>
    @endpush
</div>
@endsection


