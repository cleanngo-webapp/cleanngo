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
                    <td class="p-2">{{ $b->employee_name ?? '—' }}</td>
                    <td class="p-2 capitalize">{{ $b->status }}</td>
                    <td class="p-2">
                        <div class="flex items-center gap-2">
                            <form method="post" action="{{ url('/admin/bookings/'.$b->id.'/status') }}" class="flex items-center gap-2">
                                @csrf
                                <select name="status" class="border rounded px-2 py-1 text-sm">
                                    <option value="pending" {{ $b->status==='pending'?'selected':'' }}>Pending</option>
                                    <option value="confirmed" {{ $b->status==='confirmed'?'selected':'' }}>Confirm</option>
                                    <option value="cancelled" {{ $b->status==='cancelled'?'selected':'' }}>Cancel</option>
                                    <option value="completed" {{ $b->status==='completed'?'selected':'' }}>Completed</option>
                                </select>
                                <button type="submit" class="px-2 py-1 border rounded" onclick="this.closest('form').submit()">Update</button>
                            </form>
                            @if(empty($b->employee_name))
                                <form method="post" action="{{ url('/admin/bookings/'.$b->id.'/assign') }}" class="flex items-center gap-2 assign-form" data-booking-id="{{ $b->id }}">
                                    @csrf
                                    <select name="employee_user_id" class="border rounded px-2 py-1 text-sm">
                                        <option value="">Assign Employee</option>
                                        @foreach($employees as $e)
                                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-2 py-1 border rounded">Assign</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">{{ $bookings->links() }}</div>
    </div>

    <div id="create-booking-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
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
    <div id="assign-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
        <div class="bg-white rounded-xl w-full max-w-md p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="font-semibold">Confirm Assignment</div>
                <button id="assignModalClose">✕</button>
            </div>
            <p id="assignModalText" class="mb-4 text-sm">Are you sure you want to assign this employee? This cannot be changed later.</p>
            <div class="flex justify-end gap-2">
                <button id="assignModalCancel" class="px-3 py-2 border rounded">Cancel</button>
                <button id="assignModalConfirm" class="px-3 py-2 bg-emerald-700 text-white rounded">Confirm</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){
        let pendingAssignForm = null;
        const modal = document.getElementById('assign-modal');
        const txt = document.getElementById('assignModalText');
        function openModal(form){
            pendingAssignForm = form;
            const bid = form.getAttribute('data-booking-id');
            txt.textContent = 'Assign employee to booking '+bid+'? This cannot be changed later.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.getElementById('assignModalClose').addEventListener('click', closeModal);
        document.getElementById('assignModalCancel').addEventListener('click', closeModal);
        document.getElementById('assignModalConfirm').addEventListener('click', function(){
            if (pendingAssignForm) pendingAssignForm.submit();
            closeModal();
        });
        // Intercept submit of all assign forms
        document.querySelectorAll('.assign-form').forEach(function(f){
            f.addEventListener('submit', function(e){
                // require a selection
                const sel = f.querySelector('select[name="employee_user_id"]');
                if (!sel || !sel.value) { return; }
                e.preventDefault();
                openModal(f);
            });
        });
    })();
    </script>
    @endpush
</div>
@endsection


