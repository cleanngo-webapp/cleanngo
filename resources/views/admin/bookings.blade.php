@extends('layouts.admin')

@section('title','Bookings and Scheduling')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-center">Bookings and Scheduling</h1>

    <div class="mt-6 bg-white rounded-xl p-4">
        <div id="admin-calendar" data-events-url="{{ route('admin.calendar.events') }}"></div>
    </div>

    <div class="mt-6 overflow-auto">
        <div class="flex justify-end mb-2">
            <button class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="document.getElementById('create-booking-modal').classList.remove('hidden')">+ Add Booking and Schedule</button>
        </div>
        <table class="min-w-full bg-white rounded border text-sm">
            <thead class="bg-emerald-50">
                <tr class="text-left font-semibold">
                    <th class="p-2">Booking ID</th>
                    <th class="p-2">Date & Time</th>
                    <th class="p-2">Customer</th>
                    <th class="p-2">Assigned Employee</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr class="border-t">
                    <td class="p-2">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</td>
                    <td class="p-2">{{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('m/d/y g:i A') : '—' }}</td>
                    <td class="p-2">{{ $b->customer_name ?? '—' }}</td>
                    <td class="p-2">
                        @if(!empty($b->assigned_employee_id))
                            {{ $b->employee_name ?? '—' }}
                        @else
                            <form method="post" action="{{ url('/admin/bookings/'.$b->id.'/assign') }}" class="assign-form inline" data-booking-id="{{ $b->id }}" data-booking-code="{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}">
                                @csrf
                                <select name="employee_user_id" class="border rounded px-2 py-1 text-sm assign-select cursor-pointer">
                                    <option class="cursor-pointer" value="">Assign Employee</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="p-2">
                        <select class="border rounded px-2 py-1 text-sm status-select cursor-pointer" data-booking-id="{{ $b->id }}" data-booking-code="{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}">
                            <option value="pending" {{ $b->status==='pending'?'selected':'' }}>Pending</option>
                            <option value="in_progress" {{ $b->status==='in_progress'?'selected':'' }}>In Progress</option>
                            <option value="confirmed" {{ $b->status==='confirmed'?'selected':'' }}>Confirm</option>
                            <option value="completed" {{ $b->status==='completed'?'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ $b->status==='cancelled'?'selected':'' }}>Cancel</option>
                        </select>
                    </td>
                    <td class="p-2">
                        <div class="flex items-center gap-2">
                            <button type="button" class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="openReceipt({{ $b->id }})" title="View Receipt">
                                <span class="sr-only">View Receipt</span>
                                <i class="ri-receipt-line"></i>
                            </button>
                            <button type="button" class="px-2 py-1 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="openLocation({{ $b->id }})" title="View Location">
                                <span class="sr-only">View Location</span>
                                <i class="ri-map-pin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $bookings->links() }}</div>
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
                <button id="assignModalCancel" class="px-3 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Cancel</button>
                <button id="assignModalConfirm" class="px-3 py-2 bg-emerald-700 text-white rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white">Confirm</button>
            </div>
        </div>
        </div>

    <!-- Receipt Modal -->
    <div id="receipt-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[1000]">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Service Receipt</div>
                <button onclick="closeReceipt()">✕</button>
            </div>
            <div id="receiptBody" class="text-sm space-y-1"></div>
            <div class="mt-4 flex justify-end">
                <button class="bg-emerald-700 text-white px-3 py-2 border rounded cursor-pointer hover:bg-emerald-700/80 hover:text-white" onclick="closeReceipt()">Close</button>
            </div>
        </div>
        </div>

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
            <div id="locationAddress" class="text-sm mb-2 text-gray-700"></div>
            <div id="adminLocationMap" class="h-80 rounded border"></div>
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
    function openReceipt(bookingId){
        const data = receiptData[String(bookingId)] || receiptData[bookingId];
        const modal = document.getElementById('receipt-modal');
        const body = document.getElementById('receiptBody');
        if (!data){
            body.innerHTML = '<div class="text-sm text-gray-500">No items recorded for this booking.</div>';
        } else {
            const lines = (data.lines||[]).map(l => {
                const left = [l.item_type, (l.area_sqm? (l.area_sqm+' sqm'): null), (l.quantity? ('x '+l.quantity): null)].filter(Boolean).join(' ');
                const right = peso(l.line_total ?? ((l.unit_price||0)*(l.quantity||1)));
                return `<div class="flex justify-between"><span>${left}</span><span>${right}</span></div>`;
            }).join('');
            body.innerHTML = lines + `<div class="mt-2 flex justify-between font-semibold"><span>Total</span><span>${peso(data.total||0)}</span></div>`;
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeReceipt(){
        const modal = document.getElementById('receipt-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    // Location modal handlers and Leaflet map
    let adminMap = null; let adminMarker = null;
    function openLocation(bookingId){
        const data = locationsData[String(bookingId)] || locationsData[bookingId];
        const modal = document.getElementById('location-modal');
        const addr = document.getElementById('locationAddress');
        addr.textContent = data?.address || 'No address available';
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
        // Hook up status selectors
        document.querySelectorAll('.status-select').forEach(function(sel){
            sel.setAttribute('data-current', sel.value);
            sel.addEventListener('change', function(){
                const bid = sel.getAttribute('data-booking-id');
                const code = sel.getAttribute('data-booking-code');
                const newStatus = sel.value;
                openStatusModal(bid, code, newStatus);
            });
        });
    })();
    </script>
    @endpush
</div>
@endsection


