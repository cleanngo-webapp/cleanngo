@extends('layouts.app')

@section('title','Customer Dashboard')

@section('content')
{{-- Customer Dashboard View --}}
{{-- Purpose: Allow customers to see bookings and request new services --}}

<div class="max-w-4xl mx-auto my-10 p-6">
	<h1 class="text-2xl font-bold">Customer Dashboard</h1>
	<p class="mt-2 text-gray-600">Track your bookings and manage your addresses.</p>

	<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Upcoming Bookings</h2>
			<p class="text-sm text-gray-500">You have no upcoming bookings.</p>
		</div>
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Addresses</h2>
			<p class="text-sm text-gray-500">Add or select your service location.</p>
		</div>
	</div>
</div>
@endsection
