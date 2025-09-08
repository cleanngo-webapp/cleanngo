@extends('layouts.employee')

@section('title','Notifications')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold">Notifications</h1>

    <div class="mt-6 space-y-3">
        @foreach ([
            'New Job Assigned',
            'Reminder: Upcoming Job – Carpet Deep Cleaning at 2:00 PM...',
            'Successful payment ₱850 paid via GCash for Booking B001.',
            'Customer Ana Cruz rated you 5 stars for Sofa Cleaning....',
            'Payroll Summary Updated  You earned ₱3,200 this week...',
        ] as $note)
            <div class="bg-emerald-300 rounded-full px-4 py-3 flex justify-between items-center">
                <span>{{ $note }}</span>
                <span class="text-sm">Sep 8</span>
            </div>
        @endforeach
    </div>
</div>
@endsection


