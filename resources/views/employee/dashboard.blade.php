@extends('layouts.app')

@section('title','Employee Dashboard')

@section('content')
{{-- Employee Dashboard View --}}
{{-- Purpose: Daily jobs for cleaners, with simple instructions --}}

<div class="max-w-3xl mx-auto my-10 p-6">
	<h1 class="text-2xl font-bold">Employee Dashboard</h1>
	<p class="mt-2 text-gray-600">Your assigned jobs for today will appear here.</p>

	<div class="mt-4 p-4 rounded border bg-white">
		<p class="text-gray-700">No jobs assigned yet.</p>
	</div>
</div>
@endsection
