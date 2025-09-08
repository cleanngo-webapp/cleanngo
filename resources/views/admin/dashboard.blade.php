@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
{{-- Admin Dashboard - 2 rows x 3 cards --}}

<div class="max-w-6xl mx-auto">
	<h1 class="text-3xl font-extrabold">Dashboard</h1>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Total Bookings</h2>
		</div>
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Today's Bookings</h2>
		</div>
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Active Services</h2>
		</div>
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Employees Assigned Today</h2>
		</div>
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Completed Jobs Today</h2>
		</div>
		<div class="bg-white rounded-xl p-4 shadow-sm">
			<h2 class="font-semibold">Low Stock Items</h2>
		</div>
	</div>
</div>
@endsection
