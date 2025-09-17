@extends('layouts.app')

@section('title','Notifications')

@section('content')
<div class="max-w-6xl mx-auto mt-10 pt-7">
    <h1 class="text-3xl font-extrabold text-emerald-900">Notifications</h1>

    <div class="mt-6 space-y-3">
        @foreach ([
            'Your booking request for Carpet Deep Cleaning has been confirmed for Sep 10 at 2:00 PM',
            'Payment of ₱850 for your Sofa Cleaning service has been received via GCash',
            'Your cleaning service has been completed. Please rate your experience',
            'Reminder: Your Car Interior Detailing is scheduled for tomorrow at 10:00 AM',
            'Thank you for your 5-star rating! We appreciate your feedback',
            'New service available: Post Construction Cleaning - Book now for 20% off',
            'Your booking B001 has been assigned to John Doe - Professional Cleaner',
            'Payment confirmation: ₱1,200 received for Glass Cleaning service',
        ] as $note)
            <div class="bg-emerald-300 rounded-full px-4 py-3 flex justify-between items-center">
                <span>{{ $note }}</span>
                <span class="text-sm">Sep 8</span>
            </div>
        @endforeach
    </div>
</div>
@endsection
