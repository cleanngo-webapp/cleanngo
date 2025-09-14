@extends('layouts.employee')

@section('title','Employee Dashboard')

@section('content')
{{-- Employee Dashboard with job assignments and progress tracking --}}
{{-- Purpose: Daily jobs for cleaners, with simple instructions and progress tracking --}}

<div class="max-w-6xl mx-auto">
	<div class="flex items-center justify-between mb-8">
		<h1 class="text-3xl font-extrabold text-gray-900">Dashboard</h1>
		<div class="text-sm text-gray-500">
			{{ now()->format('l, F j, Y') }}
		</div>
	</div>

	{{-- Job Statistics Cards --}}
	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
		{{-- Jobs Assigned Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Jobs Assigned Today</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($jobsAssignedToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Scheduled for today</p>
				</div>
				<div class="bg-blue-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Jobs Completed Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Jobs Completed Today</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($jobsCompletedToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Finished today</p>
				</div>
				<div class="bg-green-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Pending Jobs Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Pending Jobs</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($pendingJobs) }}</p>
					<p class="text-xs text-gray-500 mt-1">Awaiting start</p>
				</div>
				<div class="bg-yellow-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
			</div>
		</div>
	</div>

	{{-- Today's Job Assignments --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
		<div class="p-6 border-b border-gray-100">
			<h2 class="text-xl font-semibold text-gray-900">Today's Job Assignments</h2>
			<p class="text-sm text-gray-500 mt-1">Your scheduled jobs for today</p>
		</div>
		<div class="p-6">
			@forelse($todayJobs as $job)
			<div class="border border-gray-200 rounded-lg p-4 mb-4 last:mb-0 hover:bg-gray-50 transition-colors">
				<div class="flex items-start justify-between">
					<div class="flex-1">
						<div class="flex items-center gap-3 mb-2">
							<h3 class="text-lg font-semibold text-gray-900">{{ $job->service_name }}</h3>
							@php
								$statusColors = [
									'pending' => 'bg-yellow-100 text-yellow-800',
									'confirmed' => 'bg-blue-100 text-blue-800',
									'in_progress' => 'bg-purple-100 text-purple-800',
									'completed' => 'bg-green-100 text-green-800',
									'cancelled' => 'bg-red-100 text-red-800',
									'no_show' => 'bg-gray-100 text-gray-800'
								];
							@endphp
							<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
								{{ ucfirst(str_replace('_', ' ', $job->status)) }}
							</span>
						</div>
						
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
							<div>
								<p><span class="font-medium">Customer:</span> {{ $job->first_name }} {{ $job->last_name }}</p>
								<p><span class="font-medium">Phone:</span> {{ $job->phone }}</p>
								<p><span class="font-medium">Address:</span> {{ $job->street_address }}, {{ $job->city }}</p>
							</div>
							<div>
								<p><span class="font-medium">Scheduled:</span> {{ \Carbon\Carbon::parse($job->scheduled_start)->format('g:i A') }}</p>
								@if($job->scheduled_end)
								<p><span class="font-medium">End Time:</span> {{ \Carbon\Carbon::parse($job->scheduled_end)->format('g:i A') }}</p>
								@endif
								@if($job->duration_minutes)
								<p><span class="font-medium">Duration:</span> {{ $job->duration_minutes }} minutes</p>
								@endif
							</div>
						</div>
						
						@if($job->notes)
						<div class="mt-3 p-3 bg-gray-50 rounded-lg">
							<p class="text-sm text-gray-700"><span class="font-medium">Notes:</span> {{ $job->notes }}</p>
						</div>
						@endif
					</div>
					
					<div class="ml-4 flex flex-col gap-2">
						@if($job->status === 'confirmed' || $job->status === 'pending')
						<form method="POST" action="{{ route('employee.jobs.start', $job->id) }}" class="inline">
							@csrf
							<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
								Start Job
							</button>
						</form>
						@endif
						
						@if($job->status === 'in_progress')
						<form method="POST" action="{{ route('employee.jobs.complete', $job->id) }}" class="inline">
							@csrf
							<button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
								Complete Job
							</button>
						</form>
						@endif
						
						<a href="{{ route('employee.jobs') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800">
							View Details
						</a>
					</div>
				</div>
			</div>
			@empty
			<div class="text-center py-8">
				<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
				</svg>
				<h3 class="mt-2 text-sm font-medium text-gray-900">No jobs assigned</h3>
				<p class="mt-1 text-sm text-gray-500">You don't have any jobs scheduled for today.</p>
			</div>
			@endforelse
		</div>
	</div>

	{{-- Calendar Section --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-100">
			<h2 class="text-xl font-semibold text-gray-900">Schedule Calendar</h2>
			<p class="text-sm text-gray-500 mt-1">View your upcoming job assignments</p>
		</div>
		<div class="p-6">
		<div id="employee-calendar" data-events-url="{{ route('employee.calendar.events') }}"></div>
		</div>
	</div>
</div>
@endsection
