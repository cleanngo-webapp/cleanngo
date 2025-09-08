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

    <div class="mt-8 bg-white rounded-xl border">
        <div class="p-3 font-semibold text-center">Employee Records Table</div>
        <div class="border-t grid grid-cols-7 text-sm font-semibold">
            <div class="p-2">Employee ID</div>
            <div class="p-2">Name</div>
            <div class="p-2">Specializations</div>
            <div class="p-2">Contact</div>
            <div class="p-2">Status</div>
            <div class="p-2">Jobs Assigned Today</div>
            <div class="p-2">Actions</div>
        </div>
        <div class="grid grid-cols-7 text-sm">
            <div class="p-2">E001</div>
            <div class="p-2">Jay Bro</div>
            <div class="p-2">Carpet Clean</div>
            <div class="p-2">0987683765</div>
            <div class="p-2">On Duty</div>
            <div class="p-2">1</div>
            <div class="p-2">...</div>
        </div>
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


