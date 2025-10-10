@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
{{-- Admin Dashboard with functional metrics and data display --}}
{{-- Purpose: Overview of business operations, booking statistics, and daily activities --}}

<div class="max-w-6xl mx-auto">
	<div class="flex items-center justify-between mb-8">
		<h1 class="text-3xl font-extrabold text-gray-900 text-center">Dashboard</h1>
		<div class="text-sm text-gray-500">
			{{ now()->format('l, F j, Y') }}
		</div>
	</div>

	{{-- Key Metrics Cards --}}
	<div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
		{{-- Total Bookings Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Total Bookings</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($totalBookings) }}</p>
					<p class="text-xs text-gray-500 mt-1">All time bookings</p>
				</div>
				<div class="bg-blue-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Today's Bookings Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Today's Bookings</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($todayBookings) }}</p>
					<p class="text-xs text-gray-500 mt-1">Scheduled for today</p>
				</div>
				<div class="bg-green-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Active Services Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Active Services</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($activeServices) }}</p>
					<p class="text-xs text-gray-500 mt-1">Currently in progress</p>
				</div>
				<div class="bg-yellow-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Employees Assigned Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Active Employees</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($employeesAssignedToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Scheduled today or working</p>
				</div>
				<div class="bg-purple-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Completed Jobs Today Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Completed Today</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($completedJobsToday) }}</p>
					<p class="text-xs text-gray-500 mt-1">Jobs finished today</p>
				</div>
				<div class="bg-emerald-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		{{-- Low Stock Items Card --}}
		<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-sm font-medium text-gray-600">Low Stock Items</p>
					<p class="text-3xl font-bold text-gray-900">{{ number_format($lowStockItems) }}</p>
					<p class="text-xs text-gray-500 mt-1">Need restocking</p>
				</div>
				<div class="bg-red-100 p-3 rounded-lg">
					<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
					</svg>
				</div>
			</div>
		</div>
	</div>

	{{-- Calendar Section --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
		<div class="p-4 border-b border-gray-100">
			<h2 class="text-lg font-semibold text-gray-900">Booking Calendar</h2>
			<p class="text-sm text-gray-500 mt-1">View all bookings and their schedules</p>
		</div>
		
		{{-- Color Legend --}}
		<div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
			<div class="flex items-center gap-6 text-sm">
				<span class="text-gray-600 font-medium">Status Colors:</span>
				<div class="flex items-center gap-2">
					<div class="w-4 h-4 rounded" style="background-color: #F59E0B;"></div>
					<span class="text-gray-700">Pending</span>
				</div>
				<div class="flex items-center gap-2">
					<div class="w-4 h-4 rounded" style="background-color: #3B82F6;"></div>
					<span class="text-gray-700">Confirmed</span>
				</div>
				<div class="flex items-center gap-2">
					<div class="w-4 h-4 rounded" style="background-color: #8B5CF6;"></div>
					<span class="text-gray-700">In Progress</span>
				</div>
				<div class="flex items-center gap-2">
					<div class="w-4 h-4 rounded" style="background-color: #10B981;"></div>
					<span class="text-gray-700">Completed</span>
				</div>
			</div>
		</div>
		
		<div class="p-4">
			<div id="admin-calendar" class="w-full" style="height: 350px;" data-events-url="{{ route('admin.calendar.events') }}"></div>
		</div>
	</div>

	{{-- Recent Bookings Section --}}
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-100">
			<h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
			<p class="text-sm text-gray-500 mt-1">Latest customer bookings and their status</p>
		</div>
		<div class="overflow-x-auto">
			<table class="w-full">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Service</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@forelse($recentBookings as $booking)
					<tr class="hover:bg-gray-50">
						<td class="px-6 py-4 whitespace-nowrap">
							<div class="text-sm font-medium text-gray-900">{{ $booking->code }}</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<div class="text-sm text-gray-900">{{ $booking->first_name }} {{ $booking->last_name }}</div>
						</td>
						<td class="px-6 py-4 w-48">
							<div class="text-sm text-gray-900 break-words">{{ $serviceSummaries[$booking->id] ?? ($booking->service_name ?? 'General Service') }}</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->scheduled_start)->format('M j, Y g:i A') }}</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
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
							<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
								@if($booking->status === 'in_progress')
									In Progress
								@else
									{{ ucfirst(str_replace('_', ' ', $booking->status)) }}
								@endif
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<div class="text-sm text-gray-900">₱{{ number_format($booking->total_due_cents / 100, 2) }}</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="6" class="px-6 py-12 text-center">
							<div class="flex flex-col items-center justify-center space-y-4">
								<!-- Empty State Icon -->
								<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="ri-calendar-line text-2xl text-gray-400"></i>
								</div>
								
								<!-- Empty State Content -->
								<div class="text-center">
									<h3 class="text-lg font-medium text-gray-900 mb-2">No Recent Bookings Found</h3>
									<p class="text-sm text-gray-500 mb-4">
										No recent bookings have been made yet. Bookings will appear here once customers start making reservations.
									</p>
								</div>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
