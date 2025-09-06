@extends('layouts.app')

@section('title','Staff Dashboard')

@section('content')
{{-- Staff Dashboard View --}}
{{-- Purpose: Landing for internal staff (dispatchers/office) --}}

<div class="max-w-5xl mx-auto p-6">
	<h1 class="text-2xl font-bold">Staff Dashboard</h1>
	<p class="mt-2 text-gray-600">View schedules, manage bookings, and coordinate cleaners.</p>

	<ul class="mt-4 list-disc list-inside text-gray-700">
		<li>Today’s bookings</li>
		<li>Pending confirmations</li>
		<li>Cleaner assignments</li>
	</ul>
</div>
@endsection
