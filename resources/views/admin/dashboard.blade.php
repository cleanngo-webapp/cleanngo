@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')
{{-- Admin Dashboard View --}}
{{-- Purpose: Landing page for admin users to manage the system --}}
{{-- Notes: Keep this simple; wire up actual data later. --}}

<div class="max-w-7xl mx-auto my-10 p-6">
	<h1 class="text-2xl font-bold">Admin Dashboard</h1>
	<p class="mt-2 text-gray-600">Use this area to oversee bookings, staff, inventory, and reports.</p>

	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Bookings</h2>
			<p class="text-sm text-gray-500">Upcoming schedules and assignments</p>
		</div>
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Staff</h2>
			<p class="text-sm text-gray-500">Availability and performance</p>
		</div>
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Inventory</h2>
			<p class="text-sm text-gray-500">Supply levels and usage</p>
		</div>
		<div class="p-4 rounded border bg-white">
			<h2 class="font-semibold">Reports</h2>
			<p class="text-sm text-gray-500">Daily stats and payroll summaries</p>
		</div>
	</div>
</div>
@endsection
