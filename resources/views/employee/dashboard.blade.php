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

	<div class="mt-10 bg-white rounded-xl p-4">
		<div id="employee-calendar" data-events-url="{{ route('employee.calendar.events') }}"></div>
	</div>
</div>
@endsection
