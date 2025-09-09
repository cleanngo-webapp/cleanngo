@extends('layouts.employee')

@section('title','Employee Dashboard')

@section('content')
{{-- Employee Dashboard View --}}
{{-- Purpose: Daily jobs for cleaners, with simple instructions --}}

<div class="max-w-6xl mx-auto">
	<h1 class="text-3xl font-extrabold">Dashboard</h1>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
		<div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Jobs Assigned Today</h2></div>
		<div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Jobs Completed Today</h2></div>
		<div class="bg-white rounded-xl p-4 shadow-sm"><h2 class="font-semibold">Pending Jobs</h2></div>
	</div>

	<div class="mt-6 bg-white rounded-xl border">
		<div class="p-3 font-semibold text-center">Today's Job List</div>
		<div class="grid grid-cols-7 text-sm font-semibold">
			<div class="p-2">Booking ID</div>
			<div class="p-2">Service Type</div>
			<div class="p-2">Customer Name</div>
			<div class="p-2">Time/Schedule</div>
			<div class="p-2">Status</div>
			<div class="p-2">Action</div>
			<div class="p-2"></div>
		</div>
		<div class="grid grid-cols-7 text-sm">
			<div class="p-2">B001</div>
			<div class="p-2">Sofa Cleaning</div>
			<div class="p-2">Ana Cruz</div>
			<div class="p-2">Sept 12, 10:00 AM</div>
			<div class="p-2">Pending</div>
			<div class="p-2">[ Start Job ]</div>
			<div class="p-2"></div>
		</div>
	</div>

	<div class="mt-10 bg-white rounded-xl p-4">
		<div id="employee-calendar" data-events-url="{{ route('employee.calendar.events') }}"></div>
	</div>
</div>
@endsection
