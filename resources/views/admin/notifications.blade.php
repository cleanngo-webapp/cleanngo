@extends('layouts.admin')

@section('title','Notifications')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold">Notifications</h1>

    <div class="mt-6 space-y-3">
        @foreach ([
            'New booking request from Maria Santos - Carpet Deep Cleaning',
            'Employee John Doe completed job B001 - Payment received ₱850',
            'Customer Ana Cruz rated service 5 stars for Sofa Cleaning',
            'Inventory alert: Carpet cleaner running low (3 bottles left)',
            'Payroll period ending - Review employee timesheets',
            'New customer registration: Pedro Cruz from San Felipe',
            'Booking B002 confirmed for tomorrow at 2:00 PM',
            'Payment received via GCash for Booking B003 - ₱1,200',
        ] as $note)
            <div class="bg-emerald-300 rounded-full px-4 py-3 flex justify-between items-center">
                <span>{{ $note }}</span>
                <span class="text-sm">Sep 8</span>
            </div>
        @endforeach
    </div>
</div>
@endsection
