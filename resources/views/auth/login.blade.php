{{-- Simple Login --}}

@extends('layouts.auth')

@section('title','Sign In')

@section('content')
	<div class="min-h-screen flex items-center justify-center">
		<div class="bg-white/90 rounded-xl shadow p-6 w-full max-w-md">
			<h1 class="text-2xl font-bold text-center">Sign In</h1>
			@if ($errors->any())
				<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
					{{ $errors->first() }}
				</div>
			@endif
			<form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4">
				@csrf
				<div>
					<label class="block text-sm font-medium">Email</label>
					<input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" required />
				</div>
				<div>
					<label class="block text-sm font-medium">Password</label>
					<input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required />
				</div>
				<div class="flex justify-between items-center">
					<a href="{{ route('register') }}" class="text-emerald-700 cursor-pointer">Sign Up</a>
					<button class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer" type="submit">Sign In</button>
				</div>
			</form>
		</div>
		</div>
@endsection


