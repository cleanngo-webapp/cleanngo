@extends('layouts.admin')

@section('title','Manage Employees & Payroll')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Manage Employees & Payroll</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Employees Assigned Today</h2></div>
        <div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Completed Jobs Today</h2></div>
        <div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Today's Bookings</h2></div>
    </div>

    <div class="mt-6 overflow-auto">
        <table class="min-w-full bg-white rounded border text-sm">
            <thead class="bg-emerald-50">
                <tr class="font-semibold border-b">
                    <th class="p-2 text-center text-2xl" colspan="8">Employee Records Table</th>
                </tr>
                <tr class="text-left font-semibold">
                    <th class="p-2">Employee ID</th>
                    <th class="p-2">Full Name</th>
                    <th class="p-2">Contact</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Total Bookings</th>
                    <th class="p-2">Jobs Assigned Today</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $emp)
                    <tr class="border-t">
                        <td class="p-2">{{ $emp->employee_code ?? ($emp->employee_id ? sprintf('EMP-%03d', $emp->employee_id) : '—') }}</td>
                        <td class="p-2">{{ trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) ?: $emp->username }}</td>
                        <td class="p-2">{{ $emp->contact_number ?? $emp->phone ?? '—' }}</td>
                        <td class="p-2">{{ $emp->employment_status ? ucfirst($emp->employment_status) : (($emp->is_active ?? true) ? 'Active' : 'Inactive') }}</td>
                        <td class="p-2">{{ $emp->total_bookings ?? 0 }}</td>
                        <td class="p-2">{{ $emp->jobs_assigned_today ?? 0 }}</td>
                        <td class="p-2">
                            <a href="{{ route('admin.employee.show', $emp->user_id) }}" class="px-2 py-1 border rounded inline-flex items-center gap-1 cursor-pointer hover:bg-emerald-700/80 hover:text-white" aria-label="View Employee Information">
                                <i class="ri-eye-line"></i>
                                <span class="sr-only">View</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2">{{ $employees->links() }}</div>
    </div>

    <div class="mt-8 bg-white rounded-xl border">
        <div class="p-3 font-semibold text-center">Payroll Records Table</div>
        <div class="border-t grid grid-cols-9 text-sm font-semibold">
            <div class="p-2">Date</div>
            <div class="p-2">Booking ID</div>
            <div class="p-2">Service</div>
            <div class="p-2">Customer</div>
            <div class="p-2">Employee</div>
            <div class="p-2">Pay Amount</div>
            <div class="p-2">Payment Method</div>
            <div class="p-2">Status</div>
            <div class="p-2">Actions</div>
        </div>
        <div class="grid grid-cols-9 text-sm">
            <div class="p-2">Sept 10</div>
            <div class="p-2">B001</div>
            <div class="p-2">Carpet Clean</div>
            <div class="p-2">Jay Bro</div>
            <div class="p-2">Ernie Ibarra</div>
            <div class="p-2">2,500</div>
            <div class="p-2">Gcash</div>
            <div class="p-2">Paid</div>
            <div class="p-2">...</div>
        </div>
    </div>
</div>
@endsection


