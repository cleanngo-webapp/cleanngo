@extends('layouts.admin')

@section('title','Bookings and Scheduling')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-center">Bookings and Scheduling</h1>

    <div class="max-w-3xl mx-auto mt-6 bg-white rounded-xl p-4">
        <div id="admin-calendar" class="max-w-3xl mx-auto" data-events-url="{{ route('admin.calendar.events') }}"></div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
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
                            @if($b->status === 'pending')
                                <div class="text-sm text-gray-500 italic">Confirm booking first</div>
                            @elseif($b->status === 'cancelled')
                                <div class="text-sm text-gray-500 italic">Booking cancelled</div>
                            @elseif(!empty($b->assigned_employee_id))
                                <div class="text-sm text-gray-900">{{ $b->employee_name ?? '—' }}</div>
                            @else
                                <form method="post" action="{{ url('/admin/bookings/'.$b->id.'/assign') }}" class="assign-form inline" data-booking-id="{{ $b->id }}" data-booking-code="{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}">
                                    @csrf
                                    <select name="employee_user_id" class="text-sm border-gray-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500 assign-select cursor-pointer">
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
                                    <button class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors cursor-pointer" 
                                            onclick="openConfirmModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}', 'confirm')">
                                        Confirm
                                    </button>
                                    <button class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors cursor-pointer" 
                                            onclick="openConfirmModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}', 'cancel')">
                                        Cancel
                                    </button>
                                </div>
                            @else
                                {{-- Show status with colored span --}}
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($b->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($b->status === 'in_progress') bg-yellow-100 text-yellow-800
                                    @elseif($b->status === 'completed') bg-green-100 text-green-800
                                    @elseif($b->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if($b->status === 'confirmed')
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openStatusChangeModal({{ $b->id }}, '{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}')" title="Change Status">
                                        <i class="ri-arrow-up-down-line"></i>
                                    </button>
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openAdminReceipt({{ $b->id }})" title="View Receipt">
                                    <i class="ri-receipt-line"></i>
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openLocation({{ $b->id }})" title="View Location">
                                    <i class="ri-map-pin-line"></i>                                    
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
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
        <div class="bg-white rounded-xl w-full max-w-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Customer Location</div>
                <button class="cursor-pointer" onclick="closeLocation()">✕</button>
            </div>
            <div id="locationAddress" class="text-sm mb-1 text-gray-700"></div>
            <div id="locationPhone" class="text-xs mb-2 text-gray-500"></div>
            <div id="adminLocationMap" class="h-80 rounded border"></div>
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
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button class="px-3 py-2 rounded cursor-pointer shadow-sm hover:bg-gray-50" onclick="closeStatusChangeModal()">Cancel</button>
                <button id="statusChangeConfirm" class="px-3 py-2 bg-emerald-600 text-white rounded cursor-pointer hover:bg-emerald-700">Update Status</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/bookings/${pendingConfirmAction.bookingId}/confirm`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        const action = document.createElement('input');
        action.type = 'hidden';
        action.name = 'action';
        action.value = pendingConfirmAction.action;
        
        form.appendChild(csrf);
        form.appendChild(action);
        document.body.appendChild(form);
        form.submit();
        
        closeConfirmModal();
    });
    
    // Status change modal handlers
    let pendingStatusChange = null;
    function openStatusChangeModal(bookingId, bookingCode) {
        pendingStatusChange = { bookingId, bookingCode };
        const modal = document.getElementById('status-change-modal');
        const text = document.getElementById('statusChangeModalText');
        text.textContent = `Select new status for booking ${bookingCode}:`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeStatusChangeModal() {
        const modal = document.getElementById('status-change-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingStatusChange = null;
    }
    
    // Handle status change confirmation
    document.getElementById('statusChangeConfirm').addEventListener('click', function() {
        if (!pendingStatusChange) return;
        
        const newStatus = document.getElementById('statusChangeSelect').value;
        
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
        
        form.appendChild(csrf);
        form.appendChild(status);
        document.body.appendChild(form);
        form.submit();
        
        closeStatusChangeModal();
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
            if (pendingAssignForm) pendingAssignForm.submit();
            closeModal();
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
    </script>
    @endpush
</div>
@endsection


